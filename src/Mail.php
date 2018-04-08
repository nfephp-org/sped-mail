<?php

namespace NFePHP\Mail;

/**
 * Class for sending emails related to SPED services
 *
 * @category  library
 * @package   NFePHP\Mail\Mail
 * @copyright NFePHP Copyright (c) 2016
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
        $this->xml = $xml;
        $this->pdf = $pdf;
        if ($this->isFile($xml)) {
            $this->xml = file_get_contents($xml);
        }
        if ($this->isFile($pdf)) {
            $this->pdf = file_get_contents($pdf);
        }
        //get xml data
        $this->getXmlData($this->xml);
    }
    
    /**
     * Checks if given data is file, handles mixed input
     * @param  mixed $value
     * @return boolean
     */
    private function isFile($value)
    {
        $value = strval(str_replace("\0", "", $value));
        return is_file($value);
    }
    
    /**
     * Search xml for data
     * @param string $xml
     * @throws \InvalidArgumentException
     */
    protected function getXmlData($xml)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        $root = $dom->documentElement;
        $name = $root->tagName;
        $dest = $dom->getElementsByTagName('dest')->item(0);
        $ide = $dom->getElementsByTagName('ide')->item(0);
        switch ($name) {
            case 'nfeProc':
            case 'NFe':
                $type = 'NFe';
                $this->fields->numero = $ide->getElementsByTagName('nNF')->item(0)->nodeValue;
                $this->fields->valor = $dom->getElementsByTagName('vNF')->item(0)->nodeValue;
                $this->fields->data = $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue;
                $this->subject = "NFe n. " . $this->fields->numero . " - " . $this->config->fantasy;
                break;
            case 'cteProc':
            case 'CTe':
                $type = 'CTe';
                $this->fields->numero = $ide->getElementsByTagName('nCT')->item(0)->nodeValue;
                $this->fields->valor = $dom->getElementsByTagName('vRec')->item(0)->nodeValue;
                $this->fields->data = $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue;
                $this->subject = "CTe n. " . $this->fields->numero . " - " . $this->config->fantasy;
                break;
            case 'procEventoNFe':
            case 'procEventoCTe':
                $type = 'CCe';
                $this->fields->chave = $dom->getElementsByTagName('chNFe')->item(0)->nodeValue;
                $this->fields->data = $dom->getElementsByTagName('dhEvento')->item(0)->nodeValue;
                $this->fields->correcao = $dom->getElementsByTagName('xCorrecao')->item(0)->nodeValue;
                $this->fields->conduso = $dom->getElementsByTagName('xCondUso')->item(0)->nodeValue;
                if (empty($this->fields->chave)) {
                    $this->fields->chave = $dom->getElementsByTagName('chCTe')->item(0)->nodeValue;
                }
                $this->subject = "Carta de Correção " . $this->config->fantasy;
                break;
            default:
                $type = '';
        }
        //get email adresses from xml, if exists
        //may have one address in <dest><email>
        if (!empty($dest)) {
            $this->fields->destinatario = $dest->getElementsByTagName('xNome')->item(0)->nodeValue;
            $email = !empty($dest->getElementsByTagName('email')->item(0)->nodeValue) ?
                $dest->getElementsByTagName('email')->item(0)->nodeValue : '';
        }
        if (!empty($email)) {
            $this->addresses[] = $email;
        }
        //may have others in <obsCont xCampo="email"><xTexto>fulano@yahoo.com.br</xTexto>
        $obs = $dom->getElementsByTagName('obsCont');
        foreach ($obs as $ob) {
            if (strtoupper($ob->getAttribute('xCampo')) === 'EMAIL') {
                $this->addresses[] = $ob->getElementsByTagName('xTexto')->item(0)->nodeValue;
            }
        }
        //xml may be a NFe or a CTe or a CCe nothing else
        if ($type != 'NFe' && $type != 'CTe' && $type != 'CCe') {
            $msg = "Você deve passar apenas uma NFe ou um CTe ou um CCe. "
                    . "Esse documento não foi reconhecido.";
            throw new \InvalidArgumentException($msg);
        }
        $this->type = $type;
    }
    
    
    /**
     * Set all addresses including those that exists in the xml document
     * Send email only to listed addresses ignoring all email addresses in xml
     * @param array $addresses
     */
    protected function setAddresses(array $addresses = [])
    {
        if (!empty($addresses)) {
            $this->addresses = array_merge($this->addresses, $addresses);
        }
        $this->removeInvalidAdresses();
    }
    
    /**
     * Send mail
     * If no parameter was passed, only the email address contained in
     * the xml will be used
     * @param array $addresses
     * @return boolean
     * @throws \RuntimeException
     */
    public function send(array $addresses = [])
    {
        $this->setAddresses($addresses);
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
        $this->mail->ClearAllRecipients();
        $this->mail->ClearAttachments();
        return true;
    }
    
    /**
     * Configure and send documents
     * @param \stdClass $config
     * @param type $xml
     * @param type $pdf
     * @param array $addresses
     * @param type $htmltemplate
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
        $mail->send($addresses);
        return $mail;
    }
}
