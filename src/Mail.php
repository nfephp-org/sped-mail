<?php

namespace NFePHP\Mail;

/**
 * Class for sending emails related to SPED services
 *
 * @category  NFePHP
 * @package   NFePHP\Mail\Mail
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-mail for the canonical source repository
 */

use stdClass;
use DOMDocument;
use DateTime;
use InvalidArgumentException;
use RuntimeException;
use NFePHP\Mail\Base;
use PHPMailer;
use Html2Text\Html2Text;

class Mail extends Base
{
    /**
     * config
     * @var stdClass
     */
    protected $config;
    /**
     * template user-defined
     * @var string
     */
    protected $template;
    /**
     * Type from xml document NFe, CTe or CCe
     * @var string
     */
    protected $type;
    /**
     * Addresses to send mail
     * This array should be repeated fields removed
     * @var array
     */
    protected $addresses;
    /**
     * Fields from xml
     * @var stdClass
     */
    protected $fields;
    /**
     * Html Body mail message
     * @var string
     */
    protected $body;
    /**
     * Subject for email
     * @var string
     */
    protected $subject;
    /**
     * PHPMailer class
     * @var \PHPMailer
     */
    protected $mail;
    /**
     * Xml content
     * @var string
     */
    protected $xml;
    /**
     * PDF content
     * @var string
     */
    protected $pdf;
    
    /**
     * Constructor
     * @param \stdClass $config
     */
    public function __construct(stdClass $config, PHPMailer $mailer = null)
    {
        $this->mail = $mailer;
        if (is_null($mailer)) {
            $this->mail = new PHPMailer();
        }
        $this->config = $config;
        $this->loadService($config);
        $this->fields = new stdClass();
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
     * @param stdClass $config
     */
    protected function loadService(stdClass $config)
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
    public function setTemplate($htmlTemplate)
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
        if (is_file($xml)) {
            $this->xml = file_get_contents($xml);
        }
        if (is_file($pdf)) {
            $this->pdf = file_get_contents($pdf);
        }
        //get xml data
        $this->getXmlData($this->xml);
    }
    
    /**
     * Search xml for data
     * @param string $xml
     * @throws InvalidArgumentException
     */
    protected function getXmlData($xml)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        $root = $dom->documentElement;
        $name = $root->tagName;
        switch ($name) {
            case 'nfeProc':
            case 'NFe':
                $type = 'NFe';
                $this->fields->destinatario = $dom->getElementsByTagName('dest')->item(0)
                    ->getElementsByTagName('xNome')->item(0)->nodeValue;
                $this->fields->data = $dom->getElementsByTagName('ide')->item(0)
                    ->getElementsByTagName('dhEmi')->item(0)->nodeValue;
                $this->fields->numero = $dom->getElementsByTagName('ide')->item(0)
                     ->getElementsByTagName('nNF')->item(0)->nodeValue;
                $this->fields->valor = $dom->getElementsByTagName('vNF')->item(0)->nodeValue;
                $this->subject = "NFe n. ".$this->fields->numero." - ".$this->config->fantasy;
                break;
            case 'cteProc':
            case 'CTe':
                $type = 'CTe';
                $this->fields->destinatario = $dom->getElementsByTagName('dest')->item(0)
                    ->getElementsByTagName('xNome')->item(0)->nodeValue;
                $this->fields->data = $dom->getElementsByTagName('ide')->item(0)
                    ->getElementsByTagName('dhEmi')->item(0)->nodeValue;
                $this->fields->numero = $dom->getElementsByTagName('ide')->item(0)
                    ->getElementsByTagName('nCT')->item(0)->nodeValue;
                $this->fields->valor = $dom->getElementsByTagName('vRec')->item(0)->nodeValue;
                $this->subject = "CTe n. ".$this->fields->numero." - ".$this->config->fantasy;
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
                $this->subject = "Carta de Correção ". $this->config->fantasy;
                break;
            default:
                $type = '';
        }
        //get email adresses from xml, if exists
        //may have one address in <dest><email>
        $email = !empty($dom->getElementsByTagName('email')->item(0)->nodeValue) ?
            $dom->getElementsByTagName('email')->item(0)->nodeValue : '';
        if (! empty($email)) {
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
            throw new InvalidArgumentException($msg);
        }
        $this->type = $type;
    }
    
    /**
     * Render a template with valid data
     * @param string $template
     * @param string $destinatario
     * @param string $data
     * @param string $numero
     * @param string $valor
     * @param string $chave
     * @param string $correcao
     * @param string $conduso
     * @return string
     */
    protected function renderTemplate(
        $template,
        $destinatario = '',
        $data = '',
        $numero = '',
        $valor = 0,
        $chave = '',
        $correcao = '',
        $conduso = ''
    ) {
        $dt = new \DateTime(str_replace('T', ' ', $data));
        $search = array(
            '{destinatario}',
            '{data}',
            '{numero}',
            '{valor}',
            '{emitente}',
            '{chave}',
            '{correcao}',
            '{conduso}'
        );
        $replace = array(
          $destinatario,
          $dt->format('d/m/Y'),
          $numero,
          number_format($valor, 2, ',', '.'),
          $this->config->fantasy,
          $chave,
          $correcao,
          $conduso
        );
        $template = str_replace($search, $replace, $template);
        return $template;
    }
    
    /**
     * Set all addresses including those that exists in the xml document
     * Send email only to listed addresses ignoring all email addresses in xml
     * @param array $addresses
     */
    protected function setAddresses(array $addresses = [])
    {
        if (!empty($addresses)) {
            $this->addresses = $addresses;
        }
        $this->removeInvalidAdresses();
    }
    
    /**
     * Send mail
     * If no parameter was passed, only the email address contained in
     * the xml will be used
     * @param array $addresses
     * @return boolean
     * @throws RuntimeException
     */
    public function send(array $addresses = [])
    {
        $this->setAddresses($addresses);
        if (empty($this->addresses)) {
            $msg = 'Não foram passados endereços de email validos !!';
            throw new RuntimeException($msg);
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
            throw new RuntimeException($msg);
        }
        $this->mail->ClearAllRecipients();
        $this->mail->ClearAttachments();
        return true;
    }
    
    /**
     * Remove all invalid addresses
     */
    protected function removeInvalidAdresses()
    {
        //This resulted array should be repeated fields removed
        //and all not valid strings, and also trim and strtolower strings
        $this->addresses = array_unique($this->addresses);
        $this->addresses = array_map(array($this, 'clearAddressString'), $this->addresses);
        $this->addresses = array_filter($this->addresses, array($this, 'checkEmailAddress'));
    }
    
    /**
     * Build Message
     * @return string
     */
    protected function render()
    {
        //depending on the document a different template should be loaded
        //and having data patterns appropriately substituted
        $template = $this->templates[$this->type];
        if (! empty($this->template)) {
            $template = $this->template;
        }
        return $this->renderTemplate(
            $template,
            $this->fields->destinatario,
            $this->fields->data,
            $this->fields->numero,
            $this->fields->valor,
            $this->fields->chave,
            $this->fields->correcao,
            $this->fields->conduso
        );
    }
    
    /**
     * Attach all documents to message
     */
    protected function attach()
    {
        $this->mail->addStringAttachment(
            $this->xml,
            $this->type . '.xml'
        );
        if (! empty($this->pdf)) {
            $this->mail->addStringAttachment(
                $this->xml,
                $this->type . '.pdf'
            );
        }
    }
    
    /**
     * Configure and send documents
     * @param stdClass $config
     * @param type $xml
     * @param type $pdf
     * @param array $addresses
     * @param type $htmltemplate
     * @param PHPMailer $mailer
     * @return \static
     */
    public static function sendMail(
        stdClass $config,
        $xml,
        $pdf = '',
        array $addresses = [],
        $htmltemplate = '',
        PHPMailer $mailer = null
    ) {
        $mail = new static($config, $mailer);
        $mail->loadDocuments($xml, $pdf);
        $mail->setTemplate($htmltemplate);
        $mail->send($addresses);
        return $mail;
    }
}
