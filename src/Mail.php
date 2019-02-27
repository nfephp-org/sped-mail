<?php

namespace NFePHP\Mail;

/**
 * Class for sending emails related to SPED services
 *
 * @category  library
 * @package   NFePHP\Mail\Mail
 * @copyright NFePHP Copyright (c) 2008-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-mail for the canonical source repository
 */

use NFePHP\Mail\Base;
use PHPMailer;
use Html2Text\Html2Text;

class Mail extends Base
{
    /**
     * Html Body mail message
     * @var string
     */
    public $body;
    /**
     * Subject for email
     * @var string
     */
    public $subject;

    /**
     * Constructor
     * @param \stdClass $config
     */
    public function __construct(\stdClass $config)
    {
        $this->mail = new PHPMailer();
        
        $this->config = $config;
        $this->loadService($config);
        $this->fields = new \stdClass();
        $this->fields->destinatario = '';
        $this->fields->data = '';
        $this->fields->numero = '';
        $this->fields->valor = 0;
        $this->fields->chave = '';
        $this->fields->data = '';
        $this->fields->correcao = '';
        $this->fields->conduso = '';
    }

    /**
     * Load parameters to PHPMailer class
     * @param \stdClass $config
     */
    protected function loadService(\stdClass $config)
    {
        $this->mail->CharSet = 'UTF-8';
        $this->mail->isSMTP();
        $this->mail->Host = $config->host;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $config->user;
        $this->mail->Password = $config->password;
        $this->mail->SMTPSecure = $config->secure;
        $this->mail->Port = $config->port;
        $this->mail->setFrom($config->from, $config->fantasy);
        $this->mail->addReplyTo($config->replyTo, $config->replyName);
    }

    /**
     * Sets a template for body mail
     * If no template is passed, it will be used a standard template
     * see Base::class
     * @param string $htmlTemplate
     */
    public function loadTemplate($htmlTemplate)
    {
        if ($htmlTemplate != '') {
            $this->template = $htmlTemplate;
        }
    }

    /**
     * Load the documents to send
     * XML document is required, but PDF is not
     * @param string $xml content or path NFe, CTe or CCe in XML
     * @param string $pdf content or path document from NFe, CTe or CCe
     */
    public function loadDocuments($xml, $pdf = '')
    {
        $this->xml = trim($xml);
        $this->pdf = trim($pdf);
        if ($this->isFile($this->xml)) {
            $this->xml = file_get_contents($$this->xml);
        }
        if ($this->isFile($this->pdf)) {
            $this->pdf = file_get_contents($this->pdf);
        }
        //get xml data
        $this->getXmlData($this->xml);
    }

    /**
     * Checks if given data is file
     * @param  string $value
     * @return boolean
     */
    private function isFile($value)
    {
        //se a string for maior que 500bytes, provavelmente é o conteudo
        //de um xml ou de um PDF então verificar
        if (strlen($value) > 500
            && (substr($value, 0, 1) == '<' || substr($value, 0, 5) == "%PDF-")
        ) {
            return false;
        }
        //caso contrario pode ser um path muito longo !!
        $value = strval(str_replace("\0", "", $value));
        return is_file($value);
    }

    /**
     * Send mail
     * If no parameter was passed, only the email address contained in
     * the xml will be used
     * @param array $addresses
     * @return boolean
     * @throws \RuntimeException
     */
    public function send(array $addresses = [], $include = true)
    {
        $this->setAddresses($addresses, $include);
        if (empty($this->addresses)) {
            $msg = 'Não foram passados endereços de email validos !!';
            throw new \RuntimeException($msg);
        }
        foreach ($this->addresses as $address) {
            $this->mail->addAddress($address);
        }
        $body = $this->render();
        $this->mail->isHTML(true);
        $this->mail->Subject = $this->subject;
        $this->mail->Body = $body;
        $this->mail->AltBody = Html2Text::convert($body);
        $this->attach();
        if (!$this->mail->send()) {
            $msg = 'A mensagem não pode ser enviada. Mail Error: ' . $this->mail->ErrorInfo;
            throw new \RuntimeException($msg);
        }
        $this->mail->clearAllRecipients();
        $this->mail->clearAttachments();
        return true;
    }

    /**
     * Configure and send documents
     * @param \stdClass $config
     * @param string $xml
     * @param string $pdf
     * @param array $addresses
     * @param string $htmltemplate
     * @param PHPMailer $mailer
     * @return Mail
     */
    public static function sendMail(
        \stdClass $config,
        $xml,
        $pdf = '',
        array $addresses = [],
        $htmltemplate = '',
        PHPMailer $mailer = null
    ) {
        $mail = new static($config, $mailer);
        $mail->loadDocuments($xml, $pdf);
        $mail->loadTemplate($htmltemplate);
        $mail->send($addresses, false);
        return $mail;
    }
}
