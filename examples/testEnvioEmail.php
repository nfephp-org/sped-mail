<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';


use NFePHP\Mail\Mail;

$config = new stdClass();
$config->smtpdebug = 0; //0-no 1-client 2-server 3-connection 4-lowlevel
$config->host = 'smtp.example.com.br';
$config->port = 587; //25 ou 465 ou 587
$config->smtpauth = true;
$config->user = 'fulano@example.com.br';
$config->password = 'Q!w2e3R$';
$config->secure = 'tls';
$config->authtype = ''; //CRAM-MD5, PLAIN, LOGIN, XOAUTH2
$config->from = 'fulano@example.com.br';
$config->fantasy = 'Fulanoda Silva';
$config->replyTo = 'ciclano@mail.com';
$config->replyName = 'Ciclano Moreira';
$config->smtpoptions = null; /*[
    'ssl' => [
        'verify_peer' => true,
        'verify_depth' => 3,
        'allow_self_signed' => true,
        'peer_name' => 'smtp.example.com',
        'cafile' => '/etc/ssl/ca_cert.pem',
    ]
];*/
$config->timeout = 130; //Quanto tempo aguardar a conexão para abrir, em segundos. O padrão de 5 minutos (300s) 
//é da seção RFC2821 4.5.3.2 Isso precisa ser bem alto para funcionar corretamente com hosts usando 
//greetdelay como medida anti-spam.


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
    
    //OPCIONAL 
    //passa o certificado para realizar a assinatura do email
    //file_get_contents('expired_certificate.pfx');
    //$mail->enableSignature($pfx, 'associacao');
    
    //se não for passado esse array serão enviados apenas os emails
    //que estão contidos no XML, isto se existirem
    $addresses = ['seu@email.com.br'];
    
    //envia emails, se false apenas para os endereçospassados
    //se true para todos os endereços contidos no XML e mais os indicados adicionais
    $mail->send($addresses, true);

} catch (\Exception $e) {
    echo "Falha: " . $e->getMessage();
}  



