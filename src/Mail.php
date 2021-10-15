<?php

/**
 * This file belongs to the NFePHP project
 * php version 7.0 or higher
 * 
 * @category  Library
 * @package   NFePHP\Mail\Mail
 * @author    Roberto L. Machado <linux.rlm@gmail.com>
 * @copyright 2008 NFePHP Copyright (c)
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      http://github.com/nfephp-org/sped-mail
 */

namespace NFePHP\Mail;

use NFePHP\Mail\Base;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Html2Text\Html2Text;
use NFePHP\Common\Certificate;


/**
 * Class for sending emails related to SPED services
 *
 * @category  Library
 * @package   NFePHP\Mail\Mail
 * @author    Roberto L. Machado <linux.rlm@gmail.com>
 * @copyright 2008 NFePHP Copyright (c)
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      http://github.com/nfephp-org/sped-mail
 */
final class Mail extends Base
{
    /**
     * Html Body mail message
     *
     * @var string
     */
    public $body;
    /**
     * Subject for email
     *
     * @var string
     */
    public $subject;
    /**
     * Certificate class
     *
     * @var Certificate
     */
    protected $certificate;
    /**
     * Sign email
     *
     * @var bool
     */
    protected $sign = false;

    /**
     * Constructor
     *
     * @param \stdClass                           $config configuração
     * @param null|\PHPMailer\PHPMailer\PHPMailer $mailer classe
     */
    public function __construct(\stdClass $config, PHPMailer $mailer = null)
    {
        $this->mail = $mailer;
        if (is_null($mailer)) {
            $this->mail = new PHPMailer();
        }
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
     *
     * @param \stdClass $config configuração
     * 
     * @return void
     */
    protected function loadService(\stdClass $config)
    {
        $this->mail->CharSet = 'UTF-8';
        $this->mail->isSMTP();
        $this->mail->Host = $config->host;
        $this->mail->Timeout = !empty($config->timeout)
            ? $config->timeout
            : 300;
        $this->mail->SMTPAuth = !empty($config->smtpauth)
            ? $config->smtpauth
            : false;
        $this->mail->AuthType = !empty($config->authtype)
            ? $config->authtype
            : '';
        $this->mail->SMTPSecure = !empty($config->secure)
            ? $config->secure
            : '';
                $this->mail->Port = !empty($config->port)
            ? $config->port
            : 25;
        if (!empty($config->user) && !empty($config->password)) {
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $config->user;
            $this->mail->Password = $config->password;
        }
        if (!empty($config->smtpoptions) && is_array($config->smtpoptions)) {
            $this->mail->SMTPOptions = $config->smtpoptions;
        }
        $this->mail->SMTPDebug = isset($config->smtpdebug)
            ? $config->smtpdebug
            : 0;
        $this->mail->Debugoutput = !empty($config->debugoutput)
            ? $config->debugoutput
            : '';
        $this->mail->setFrom($config->from, $config->fantasy);
        $this->mail->addReplyTo($config->replyTo, $config->replyName);
    }

    /**
     * Sets a template for body mail
     * If no template is passed, it will be used a standard template
     * see Base::class
     *
     * @param string $htmlTemplate htlm da mensagem
     * 
     * @return void
     */
    public function loadTemplate($htmlTemplate = '')
    {
        if ($htmlTemplate != '') {
            $this->template = $htmlTemplate;
        }
    }
    
    /**
     * Enable S/MIME signed mail
     *
     * @param null|string $pfx      certificado pfx
     * @param null|string $password senha
     * 
     * @return void
     */
    public function enableSignature($pfx = null, $password = null)
    {
        if (empty($pfx) || empty($password)) {
            $this->sign = false;
            return;
        }
        $this->certificate = Certificate::readPfx($pfx, $password);
        $this->sign = true;
    }

    /**
     * Load the documents to send
     * XML document is required, but PDF is not
     *
     * @param string $xml content or path NFe, CTe or CCe in XML
     * @param string $pdf content or path document from NFe, CTe or CCe
     * 
     * @return void
     */
    public function loadDocuments($xml, $pdf = '')
    {
        $this->xml = trim($xml);
        $this->pdf = trim($pdf);
        if ($this->isFile($this->xml)) {
            $this->xml = file_get_contents($this->xml);
        }
        if ($this->isFile($this->pdf)) {
            $this->pdf = file_get_contents($this->pdf);
        }
        //get xml data
        $this->getXmlData($this->xml);
    }

    /**
     * Checks if given data is file
     *
     * @param string $value valor
     * 
     * @return boolean
     */
    protected function isFile($value)
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
     *
     * @param array $addresses endereços
     * @param bool  $include   adiciona endereços ao recuperado do xml
     * 
     * @return boolean
     * 
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
        if ($this->sign) {
            $dir = sys_get_temp_dir();
            $cert = tempnam($dir, 'cert_'). '.pem';
            $key = tempnam($dir, 'key_') . '.pem';
            file_put_contents($cert, "{$this->certificate->publicKey}");
            file_put_contents($key, "{$this->certificate->privateKey}");
            $this->mail->sign(
                $cert,
                $key,
                '',
                ''
            );
        }
        if (!$this->mail->send()) {
            $msg = 'A mensagem não pode ser enviada. Mail Error: '
                . $this->mail->ErrorInfo;
            !empty($cert) ? unlink($cert) : null;
            !empty($key) ? unlink($key) : null;
            throw new \RuntimeException($msg);
        }
        $this->mail->clearAllRecipients();
        $this->mail->clearAttachments();
        !empty($cert) ? unlink($cert) : null;
        !empty($key) ? unlink($key) : null;
        return true;
    }

    /**
     * Configure and send documents
     *
     * @param \stdClass      $config       coniguração
     * @param string         $xml          xml do doc
     * @param string         $pdf          pdf do doc
     * @param array          $addresses    endereços
     * @param string         $htmltemplate template
     * @param null|string    $pfx          certificado
     * @param null|string    $password     senha
     * @param null|PHPMailer $mailer       classe
     * 
     * @return Mail
     */
    public static function sendMail(
        \stdClass $config,
        $xml,
        $pdf = '',
        array $addresses = [],
        $htmltemplate = '',
        $pfx = null,
        $password = null,
        PHPMailer $mailer = null
    ) {
        $mail = new static($config, $mailer);
        $mail->loadDocuments($xml, $pdf);
        $mail->loadTemplate($htmltemplate);
        $mail->enableSignature($pfx, $password);
        $mail->send($addresses, false);
        return $mail;
    }
}
