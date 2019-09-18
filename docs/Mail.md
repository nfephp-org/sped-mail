# NFePHP\Mail\Mail::class

Esta classe é de uso opcional mas pode simplificar o envio dos emails OBRIGATÓRIOS, estabelecidos pela legislação do projeto SPED da Receita Federal.

# Forma de USO

```php

use NFePHP\Mail\Mail;

$config = new stdClass();
$config->host = 'smtp.test.com.br';
$config->user = 'usuario@test.com.br';
$config->password = 'senha';
$config->secure = 'tls';
$config->port = 587;
$config->from = 'usuario@test.com.br';
$config->fantasy = 'Test Ltda';
$config->replyTo = 'vendas@test.com.br';
$config->replyName = 'Vendas';

try {
    //a configuração é uma stdClass com os campos acima indicados
    //esse parametro é OBRIGATÓRIO
    $mail = new Mail($config);
    
    //use isso para inserir seu próprio template HTML com os campos corretos 
    //para serem substituidos em execução com os dados dos xml
    $htmlTemplate = '';
    $mail->loadTemplate($htmlTemplate);

    //aqui são passados os documentos, tanto pode ser um path como o conteudo
    //desses documentos
    $xml = 'nfe.xml';
    $pdf = '';//não é obrigatório passar o PDF, tendo em vista que é uma BOBAGEM
    $mail->loadDocuments($xml, $pdf);
    
    //se não for passado esse array serão enviados apenas os emails
    //que estão contidos no XML, isto se existirem
    $addresses = ['seu@email.com.br'];
    //se esse array for passado serão enviados emails para os endereços indicados apenas
    //e os endereços contidos no xml serão ignorados
    
    //envia emails
    $mail->send($addresses);
    
} catch (\InvalidArgumentException $e) {
    echo "Falha: " . $e->getMessage();
} catch (\RuntimeException $e) {
    echo "Falha: " . $e->getMessage();
} catch (\Exception $e) {
    echo "Falha: " . $e->getMessage();
}  

```

# Forma de USO Estática

```php

use NFePHP\Mail\Mail;

$config = new stdClass();
$config->host = 'smtp.test.com.br';
$config->user = 'usuario@test.com.br';
$config->password = 'senha';
$config->secure = 'tls';
$config->port = 587;
$config->from = 'usuario@test.com.br';
$config->fantasy = 'Test Ltda';
$config->replyTo = 'vendas@test.com.br';
$config->replyName = 'Vendas';

try {
    //paramtros:
    //config - (obrigatório) vide acima
    //xml - (obrigatório) documento a ser enviado NFe, NFCe, CTe, ou CCe, pode ser um path ou o arquivo em string
    //pdf - (opcional) documento pdf a ser enviado DANFE, DANFCE, DACTE, ou DACCE, pode ser um path ou o arquivo em string
    //enderecos - (opcional) array com os endereços de email adicionais para envio
    //template = (opcional) template HTML a ser usado 
    $resp = Mail::sendMail($config, 'nfe.xml', '', ['recebedor@outro.com.br'], '');

} catch (\InvalidArgumentException $e) {
    echo "Falha: " . $e->getMessage();
} catch (\RuntimeException $e) {
    echo "Falha: " . $e->getMessage();
} catch (\Exception $e) {
    echo "Falha: " . $e->getMessage();
}  
```


# Métodos

## function __construct($config)

Médoto construtor, recebe como parâmtro uma **stdClass** com os seguintes dados:
  
- $config->host = '<Endereço do HOST SMTP>';
- $config->user = '<identificação no usuário>';
- $config->password = '<senha de acesso>';
- $config->secure = '<nivel de segurança>';
- $config->port = <numero da porta>;
- $config->from = '<endereço da caixa postal do remetente>';
- $config->fantasy = '<Nome simplificado da Empresa>';
- $config->replyTo = '<caixa postal que podera receber uma resposta>';
- $config->replyName = '<Nome desse contato>';

## function loadDocuments($xml, $pdf)

Este método permite o carregamento dos documentos que devem ser anexados ao(s) email(s).

- $xml = (OBRIGATÓRIO) path do documento ou o próprio conteúdo do xml em uma string
- $pdf = (OPCIONAL) path do documento ou o próprio conteúdo do pdf em uma string

## function loadTemplate($html)

Este método permite que seja criado o SEU próprio template para a formação da mensagem de email.

Os templates originais estão contidos na classe src/Base.php, e segue o seguinte padrão:

```html
<p><b>Prezados {destinatario},</b></p>" 
<p>Você está recebendo a Nota Fiscal Eletrônica emitida em {data} com o número
{numero}, de {emitente}, no valor de R$ {valor}
Junto com a mercadoria, você receberá também um DANFE (Documento
Auxiliar da Nota Fiscal Eletrônica), que acompanha o trânsito das mercadorias.</p>
<p><i>Podemos conceituar a Nota Fiscal Eletrônica como um documento
de existência apenas digital, emitido e armazenado eletronicamente,
com o intuito de documentar, para fins fiscais, uma operação de
circulação de mercadorias, ocorrida entre as partes. Sua validade
jurídica garantida pela assinatura digital do remetente (garantia
de autoria e de integridade) e recepção, pelo Fisco, do documento
eletrônico, antes da ocorrência do Fato Gerador.</i></p>
<p><i>Os registros fiscais e contábeis devem ser feitos, a partir
do próprio arquivo da NF-e, anexo neste e-mail, ou utilizando o
DANFE, que representa graficamente a Nota Fiscal Eletrônica.
A validade e autenticidade deste documento eletrônico pode ser
verificada no site nacional do projeto (www.nfe.fazenda.gov.br),
através da chave de acesso contida no DANFE.</i></p>
<p><i>Para poder utilizar os dados descritos do DANFE na
escrituração da NF-e, tanto o contribuinte destinatário,
como o contribuinte emitente, terão de verificar a validade da NF-e.
Esta validade está vinculada à efetiva existência da NF-e nos
arquivos da SEFAZ, e comprovada através da emissão da Autorização de Uso.</i></p>
<p><b>O DANFE não é uma nota fiscal, nem substitui uma nota fiscal,
servindo apenas como instrumento auxiliar para consulta da NF-e no
Ambiente Nacional.</b></p>
<p>Para mais detalhes, consulte: <a href="http://www.nfe.fazenda.gov.br/">www.nfe.fazenda.gov.br</a></p>
<br>
<p>Atenciosamente,</p>
<p>{emitente}</p>
```

Repare que esse template tem as seguintes variáveis que serão substituidas em tempo de execução pela classe, com os dados contidos no próprio XML fornecido:

- {destinatario} = Razão Social do destinatário (ex. xNome)
- {data} = Data de emissão do documento (ex. dhEmi)
- {numero} = Numero do documentos (ex. nNF)
- {emitente} = Razão Social do emitente (ex. xNome)
- {valor} = Valor total do documento (ex.vNF)


## function send($addresses)

Método que realiza o envio do email aos destinatários. O parâmetro $addresses é um array com os endereços das caixas postais dos destinatários.

$addresses = (OPCIONAL) ['<endereços de email>']

> NOTA: A classe irá em primeiro lugar extrair os emails contidos no XML e a estes adicionar os contidos nesse array $addresses.
> O xml padrão pode conter apenas UM endereço de email na TAG \<dest\>, mas pode ser inclusos muitos outros usando a TAG \<obsCont\>, da seguinte forma:

```xml
<obsCont xCampo="email">
    <xTexto>fulano@yahoo.com.br</xTexto>
</obsCont>
<obsCont xCampo="email">
    <xTexto>cilcano@yahoo.com.br</xTexto>
</obsCont>
<obsCont xCampo="email">
    <xTexto>beltrano@yahoo.com.br</xTexto>
</obsCont>
```

> NOTA: Antes do envio os endereços repetidos são REMOVIDOS e validados quanto ao seu conteúdo, os que não passarem na validação também serão REMOVIDOS. 
 

## function static sendMail($config,$xml,$pdf,$addresses,$htmltemplate)

- $config = (OBRIGATÓRIO) stdClass (já descrita anteriormente)
- $xml = (OBRIGATÓRIO) path do documento ou o próprio conteúdo do xml em uma string
- $pdf = (OPCIONAL) path do documento ou o próprio conteúdo do pdf em uma string
- $addresses = (OPCIONAL) ['<endereços de email>']
- $htmltemplate = (OPCIONAL) string contendo um template alternativo

> NOTA: não está prevista a inclusão de imagens estaticas ou logos no corpo do email, se isso for desejável o template já deverá contê-las.
