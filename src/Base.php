<?php

namespace NFePHP\Mail;

class Base
{
    /**
     * Html Templates
     * @var array
     */
    protected $templates = [
        'NFe'=> "<p><b>Prezados {destinatario},</b></p>"
            . "<p>Você está recebendo a Nota Fiscal Eletrônica emitida em {data} com o número "
            . "{numero}, de {emitente}, no valor de R$ {valor}. "
            . "Junto com a mercadoria, você receberá também um DANFE (Documento "
            . "Auxiliar da Nota Fiscal Eletrônica), que acompanha o trânsito das mercadorias.</p>"
            . "<p><i>Podemos conceituar a Nota Fiscal Eletrônica como um documento "
            . "de existência apenas digital, emitido e armazenado eletronicamente, "
            . "com o intuito de documentar, para fins fiscais, uma operação de "
            . "circulação de mercadorias, ocorrida entre as partes. Sua validade "
            . "jurídica garantida pela assinatura digital do remetente (garantia "
            . "de autoria e de integridade) e recepção, pelo Fisco, do documento "
            . "eletrônico, antes da ocorrência do Fato Gerador.</i></p>"
            . "<p><i>Os registros fiscais e contábeis devem ser feitos, a partir "
            . "do próprio arquivo da NF-e, anexo neste e-mail, ou utilizando o "
            . "DANFE, que representa graficamente a Nota Fiscal Eletrônica. "
            . "A validade e autenticidade deste documento eletrônico pode ser "
            . "verificada no site nacional do projeto (www.nfe.fazenda.gov.br), "
            . "através da chave de acesso contida no DANFE.</i></p>"
            . "<p><i>Para poder utilizar os dados descritos do DANFE na "
            . "escrituração da NF-e, tanto o contribuinte destinatário, "
            . "como o contribuinte emitente, terão de verificar a validade da NF-e. "
            . "Esta validade está vinculada à efetiva existência da NF-e nos "
            . "arquivos da SEFAZ, e comprovada através da emissão da Autorização de Uso.</i></p>"
            . "<p><b>O DANFE não é uma nota fiscal, nem substitui uma nota fiscal, "
            . "servindo apenas como instrumento auxiliar para consulta da NF-e no "
            . "Ambiente Nacional.</b></p>"
            . "<p>Para mais detalhes, consulte: <a href=\"http://www.nfe.fazenda.gov.br/\">"
            . "www.nfe.fazenda.gov.br</a></p>"
            . "<br>"
            . "<p>Atenciosamente,</p>"
            . "<p>{emitente}</p>",
        
        'CTe'=> "<p><b>Prezados {destinatario},</b></p>"
            . "<p>Você está recebendo um Conhecimento de Transporte Eletrônico emitido em {data} com o número "
            . "{numero}, de {emitente}, no valor de R$ {valor}. "
            . "Junto com a mercadoria, você receberá também um DACTE (Documento "
            . "Auxiliar do Conhecimento de Transporte Eletrônico), que acompanha o trânsito das mercadorias.</p>"
            . "<p><i>Podemos conceituar o CTe como um documento "
            . "de existência apenas digital, emitido e armazenado eletronicamente, "
            . "com o intuito de documentar, para fins fiscais, uma operação de "
            . "circulação de mercadorias, ocorrida entre as partes. Sua validade "
            . "jurídica garantida pela assinatura digital do remetente (garantia "
            . "de autoria e de integridade) e recepção, pelo Fisco, do documento "
            . "eletrônico, antes da ocorrência do Fato Gerador.</i></p>"
            . "<p><i>Os registros fiscais e contábeis devem ser feitos, a partir "
            . "do próprio arquivo da NF-e, anexo neste e-mail, ou utilizando o "
            . "DACTE, que representa graficamente o Conhecimento de Transporte Eletrônico. "
            . "A validade e autenticidade deste documento eletrônico pode ser "
            . "verificada no site nacional do projeto (www.cte.fazenda.gov.br), "
            . "através da chave de acesso contida no DACTE.</i></p>"
            . "<p><i>Para poder utilizar os dados descritos do DACTE na "
            . "escrituração do CT-e, tanto o contribuinte destinatário, "
            . "como o contribuinte emitente, terão de verificar a validade do CT-e. "
            . "Esta validade está vinculada à efetiva existência do CT-e nos "
            . "arquivos da SEFAZ, e comprovada através da emissão da Autorização de Uso.</i></p>"
            . "<p><b>O DACTE não é um Conhecimento de transporte, nem o substitui, "
            . "servindo apenas como instrumento auxiliar para consulta do CT-e no "
            . "Ambiente Nacional.</b></p>"
            . "<p>Para mais detalhes, consulte: <a href=\"http://www.cte.fazenda.gov.br/\">"
            . "www.cte.fazenda.gov.br</a></p>"
            . "<br>"
            . "<p>Atenciosamente,</p>"
            . "<p>{emitente}</p>",
                
        'CCe'=> "<p><b>Prezados,</b></p>"
            . "<p>Você está recebendo uma Carta de Correção referente ao nosso documento "
            . "{chave}.</p><p>Essa carta de correção datada de {data} procura corrigir:</p> "
            . "<p><b>{correcao}</b></p>"
            . "<p><i>{conduso}</i></p>"
            . "<p>Atenciosamente,</p>"
            . "<p>{emitente}</p>",
        
        'CTeOS' => "<p><b>Prezados {destinatario},</b></p>"
            . "<p>Você está recebendo um Conhecimentode Transporte Eletrônico "
            . "para Outros Serviços emitido em {data} com o número "
            . "{numero}, de {emitente}, no valor de R$ {valor}. "
            . "Junto com a mercadoria, você receberá também um DACTEOS (Documento "
            . "Auxiliar do Conhecimentode Transporte Eletrônico para Outros "
            . "Serviços), que acompanha o trânsito das mercadorias.</p>"
            . "<p><i>Podemos conceituar o CTe-OS como um documento "
            . "de existência apenas digital, emitido e armazenado eletronicamente, "
            . "com o intuito de documentar, para fins fiscais, uma operação de "
            . "circulação de mercadorias, ocorrida entre as partes. Sua validade "
            . "jurídica garantida pela assinatura digital do remetente (garantia "
            . "de autoria e de integridade) e recepção, pelo Fisco, do documento "
            . "eletrônico, antes da ocorrência do Fato Gerador.</i></p>"
            . "<p><i>Os registros fiscais e contábeis devem ser feitos, a partir "
            . "do próprio arquivo da CTe-OS, anexo neste e-mail, ou utilizando o "
            . "DACTEOS, que representa graficamente o Conhecimentode Transporte Eletrônico para Outros Serviços. "
            . "A validade e autenticidade deste documento eletrônico pode ser "
            . "verificada no site nacional do projeto (www.cte.fazenda.gov.br), "
            . "através da chave de acesso contida no DACTEOS.</i></p>"
            . "<p><i>Para poder utilizar os dados descritos do DACTEOS na "
            . "escrituração do CTe-OS, tanto o contribuinte destinatário, "
            . "como o contribuinte emitente, terão de verificar a validade do CT-e. "
            . "Esta validade está vinculada à efetiva existência do CTe-OS nos "
            . "arquivos da SEFAZ, e comprovada através da emissão da Autorização de Uso.</i></p>"
            . "<p><b>O DACTEOS não é um Conhecimento de transporte, nem o substitui, "
            . "servindo apenas como instrumento auxiliar para consulta do CTe-OS no "
            . "Ambiente Nacional.</b></p>"
            . "<p>Para mais detalhes, consulte: <a href=\"http://www.cte.fazenda.gov.br/\">"
            . "www.cte.fazenda.gov.br</a></p>"
            . "<br>"
            . "<p>Atenciosamente,</p>"
            . "<p>{emitente}</p>",
    ];
    
    /**
     * template user-defined
     * @var string
     */
    public $template;
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
    protected $addresses = [];
    /**
     * Assunto do email
     * @var string
     */
    protected $subject;
    /**
     * Fields from xml
     * @var \stdClass
     */
    public $fields;
    /**
     * PHPMailer class
     * @var \PHPMailer
     */
    protected $mail;
    /**
     * Xml content
     * @var string
     */
    public $xml;
    /**
     * PDF content
     * @var string
     */
    public $pdf;
    /**
     * config
     * @var \stdClass
     */
    protected $config;

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
                $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
                $this->fields->id = substr($infNFe->getAttribute('Id'), 3) . '-' . strtolower($name);
                $this->fields->numero = $ide->getElementsByTagName('nNF')->item(0)->nodeValue;
                $this->fields->valor = $dom->getElementsByTagName('vNF')->item(0)->nodeValue;
                $this->fields->data = $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue;
                $this->subject = "NFe n. {$this->fields->numero} - {$this->config->fantasy}";
                break;
            case 'cteProc':
            case 'CTe':
                $type = 'CTe';
                $infCte = $dom->getElementsByTagName('infCte')->item(0);
                $this->fields->id = substr($infCte->getAttribute('Id'), 3) . '-' . strtolower($name);
                $this->fields->numero = $ide->getElementsByTagName('nCT')->item(0)->nodeValue;
                $this->fields->valor = $dom->getElementsByTagName('vRec')->item(0)->nodeValue;
                $this->fields->data = $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue;
                $this->subject = "CTe n. {$this->fields->numero} - {$this->config->fantasy}";
                break;
            case 'cteOSProc':
            case 'CTeOS':
                $type = 'CTeOS';
                $infCte = $dom->getElementsByTagName('infCte')->item(0);
                $this->fields->id = substr($infCte->getAttribute('Id'), 3) . '-' . strtolower($name);
                $this->fields->numero = $ide->getElementsByTagName('nCT')->item(0)->nodeValue;
                $this->fields->valor = $dom->getElementsByTagName('vRec')->item(0)->nodeValue;
                $this->fields->data = $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue;
                $this->subject = "CTe-OS n. {$this->fields->numero} - {$this->config->fantasy}";
                break;
            case 'procEventoNFe':
            case 'procEventoCTe':
                $type = 'CCe';
                $this->fields->chave = $dom->getElementsByTagName('chNFe')->item(0)->nodeValue;
                $this->fields->id = $this->fields->chave.'-procCCe-'.strtolower(substr($name, -3));
                $this->fields->data = $dom->getElementsByTagName('dhEvento')->item(0)->nodeValue;
                $this->fields->correcao = $dom->getElementsByTagName('xCorrecao')->item(0)->nodeValue;
                $this->fields->conduso = $dom->getElementsByTagName('xCondUso')->item(0)->nodeValue;
                if (empty($this->fields->chave)) {
                    $this->fields->chave = $dom->getElementsByTagName('chCTe')->item(0)->nodeValue;
                }
                $this->subject = "Carta de Correção {$this->config->fantasy}";
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
            // if recieve more than one e-mail address.
            if (strpos($email, ';')) {
                $emails = explode(';', $email);

                $emails = array_map(function ($item) {
                    return trim($item);
                }, $emails);

                $this->addresses = array_merge($this->addresses, $emails);
            } else {
                $this->addresses[] = $email;
            }
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
     * @param bool $include
     */
    protected function setAddresses(array $addresses = [], $include = true)
    {
        if (!empty($addresses)) {
            if (!empty($this->addresses) && $include) {
                $this->addresses = array_merge($this->addresses, $addresses);
            } else {
                $this->addresses = $addresses;
            }
        }
        $this->removeInvalidAdresses();
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
        if (!empty($this->template)) {
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
            $this->fields->id . '.xml'
        );
        if (!empty($this->pdf)) {
            $this->mail->addStringAttachment(
                $this->pdf,
                $this->fields->id. '.pdf',
                'base64',
                'application/pdf'
            );
        }
    }
    
    /**
     * Returns only valid email string
     * @param string $email
     * @return boolean
     */
    protected function checkEmailAddress($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Format email address string removing garbage and
     * set to lower characters
     * @param string $email
     * @return string
     */
    protected function clearAddressString($email)
    {
        return preg_replace('/[ ,;:]+/', '', strtolower($email));
    }
}
