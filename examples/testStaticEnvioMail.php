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
$config->password = 'senha';
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
    //OPCIONAL
    //$pfx = file_get_contents('expired_certificate.pfx');
    //$password = 'associacao';
    $pfx = null;
    $password = null;
    
    //parametros:
    //config    - (obrigatório) vide acima
    //xml       - (obrigatório) documento a ser enviado NFe, NFCe, CTe, ou CCe, pode ser um path ou o arquivo em string
    //pdf       - (opcional) documento a ser enviado DANFE, DANFCE, DACTE, ou DACCE, pode ser um path ou o arquivo em string
    //enderecos - (opcional) array com os endereços de email adicionais para envio
    //template  - (opcional) template HTML a ser usado
    //pfx       - (opcional) conteúdo do certificado pfx em string
    //password  - (opcional) senha do certificado
    //mailer    - (opcional) outra instancia do PHPMailer
    $resp = Mail::sendMail($config, 'nfe.xml', '', ['recebedor@outro.com.br'], '', $pfx, $password, null);

} catch (\Exception $e) {
    echo "Falha: " . $e->getMessage();
}  
