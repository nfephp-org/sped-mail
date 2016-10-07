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
use NFePHP\Mail\Base;

class Mail extends Base
{
    /**
     * template user-defined
     * @var string
     */
    protected $template;
    /**
     * Addresses to send mail
     * This array should be repeated fields removed
     * @var array
     */
    protected $addresses;
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
    public function __construct(stdClass $config)
    {
        $this->loadService($config);
    }
    
    /**
     * Load parameters to PHPMailer class
     * @param stdClass $config
     */
    private function loadService(stdClass $config)
    {
        $this->mail = new \PHPMailer();
        $this->mail->isSMTP();
        $this->mail->Host = $config->mail->host;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $config->mail->user;
        $this->mail->Password = $config->mail->password;
        $this->mail->SMTPSecure = $config->mail->secure;
        $this->mail->Port = $config->mail->port;
        $this->mail->setFrom($config->mail->from, $config->mail->fantasy);
        $this->mail->addReplyTo($config->mail->replyTo, $config->mail->replyName);
    }
    
    /**
     * Sets a template for body mail
     * If no template is passed, it will be used a standard template
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
     * @throws \InvalidArgumentException
     */
    private function getXmlData($xml)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        $root = $dom->documentElement;
        $name = $root->tagName;
        switch ($name) {
            case 'nfeProc':
            case 'NFe':
                $id = 'NFe';
                $destinatario = $dom->getElementsByTagName('dest')->item(0)
                    ->getElementsByTagName('xNome')->item(0)->nodeValue;
                $data = $dom->getElementsByTagName('ide')->item(0)
                    ->getElementsByTagName('dhEmi')->item(0)->nodeValue;
                $numero = $dom->getElementsByTagName('ide')->item(0)
                     ->getElementsByTagName('nNF')->item(0)->nodeValue;
                $valor = $dom->getElementsByTagName('vNF')->item(0)->nodeValue;
                $emitente = $dom->getElementsByTagName('emit')->item(0)
                    ->getElementsByTagName('xNome')->item(0)->nodeValue;
                $this->subject = "NFe n. $numero $emitente";
                break;
            case 'cteProc':
            case 'CTe':
                $id = 'CTe';
                $destinatario = $dom->getElementsByTagName('dest')->item(0)
                    ->getElementsByTagName('xNome')->item(0)->nodeValue;
                $data = $dom->getElementsByTagName('ide')->item(0)
                    ->getElementsByTagName('dhEmi')->item(0)->nodeValue;
                $numero = $dom->getElementsByTagName('ide')->item(0)
                    ->getElementsByTagName('nCT')->item(0)->nodeValue;
                $valor = $dom->getElementsByTagName('vRec')->item(0)->nodeValue;
                $emitente = $dom->getElementsByTagName('emit')->item(0)
                    ->getElementsByTagName('xNome')->item(0)->nodeValue;
                $this->subject = "CTe n. $numero $emitente";
                break;
            case 'procEventoNFe':
            case 'procEventoCTe':
                $id = 'CCe';
                $chave = $dom->getElementsByTagName('chNFe')->item(0)->nodeValue;
                $data = $dom->getElementsByTagName('dhEvento')->item(0)->nodeValue;
                $correcao = $dom->getElementsByTagName('xCorrecao')->item(0)->nodeValue;
                $conduso = $dom->getElementsByTagName('xCondUso')->item(0)->nodeValue;
                if (empty($chave)) {
                    $chave = $dom->getElementsByTagName('chCTe')->item(0)->nodeValue;
                }
                $this->subject = "Carta de Correção: $chave";
                break;
            default:
                $id = '';
        }
        //get email adresses from xml, if exists
        //may have one address in <dest><email>
        $mail = $dom->getElementsByTagName('email')->item(0)->nodeValue;
        if (! empty($mail)) {
            $this->addresses[] = $mail;
        }
        //may have others in <obsCont xCampo="email"><xTexto>fulano@yahoo.com.br</xTexto>
        $obs = $dom->getElementsByTagName('obsCont');
        foreach ($obs as $ob) {
            if (strtoupper($ob->getAttribute('xCampo')) === 'EMAIL') {
                $this->addresses[] = $ob->getElementsByTagName('xTexto')->item(0)->nodeValue;
            }
        }
        //xml may be a NFe or a CTe or a CCe nothing else
        if ($id != 'NFe' && $id != 'CTe' && $id != 'CCe') {
            $msg = "Você deve passar apenas uma NFe ou um CTe ou um CCe. "
                    . "Esse documento não foi reconhecido.";
            throw new \InvalidArgumentException($msg);
        }
        //depending on the document a different template should be loaded
        //and having data patterns appropriately substituted
        $template = $this->templates[$id];
        if (! empty($this->template)) {
            $template = $this->template;
        }
        $this->body = $this->renderTemplate(
            $template,
            $destinatario,
            $data,
            $numero,
            $valor,
            $emitente,
            $chave,
            $correcao,
            $conduso
        );
    }
    
    /**
     * Render a template with valid data
     * @param string $template
     * @param string $destinatario
     * @param string $data
     * @param string $numero
     * @param string $valor
     * @param string $emitente
     * @param string $chave
     * @param string $correcao
     * @param string $conduso
     * @return string
     */
    private function renderTemplate(
        $template,
        $destinatario = '',
        $data = '',
        $numero = '',
        $valor = '',
        $emitente = '',
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
            '{emitente}'
        );
        $replace = array(
          $destinatario,
          $dt->format('d/m/Y'),
          $numero,
          number_format($valor, 2, ',', '.'),
          $emitente
        );
        $template = str_replace($search, $replace, $template);
        return $template;
    }
    
    /**
     * Set all addresses including those that exists in the xml document
     * @param array $addresses
     */
    private function setAddresses(array $addresses = [])
    {
        $this->addresses[] = $addresses;
    }
    
    /**
     * Send mail
     * If no parameter was passed only the email address contained in
     * the xml will be used, if there is
     * @param array $addresses
     */
    public function send(array $addresses = [])
    {
        $this->setAddresses($addresses);
        //This resulted array should be repeated fields removed
        $this->addresses = array_unique($this->addresses);
        foreach ($this->addresses as $address) {
            $this->mail->addAddress($address);
        }
        $mail->isHTML(true);
        $mail->Subject = $this->subject;
        $mail->Body    = $this->body;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }
    
    /**
     * Attach all documents to message
     */
    private function attach()
    {
        $this->mail->addStringAttachment(
            $this->xml,
            'document.xml'
        );
        if (! empty($this->pdf)) {
            $this->mail->addStringAttachment(
                $this->xml,
                'document.pdf'
            );
        }
    }
    
    /**
     * Configure and send documents
     * @param stdClass $config
     * @param string $xml
     * @param string $pdf
     * @param array $addresses
     * @param string $htmlTemplate
     */
    public static function sendMail(stdClass $config, $xml, $pdf = '', array $addresses = [], $htmltemplate = '')
    {
        $mail = new static($config);
        $mail->loadDocuments($xml, $pdf);
        $mail->setTemplate($htmltemplate);
        $mail->send($addresses);
        return $mail;
    }
}
