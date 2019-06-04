<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

$config = new stdClass();
$config->host = 'smtp.test.com.br';
$config->port = 587;
$config->smtpauth = true;
$config->user = 'usuario@test.com.br';
$config->password = 'senha';
$config->secure = 'tls';
$config->authtype = null; //CRAM-MD5, PLAIN, LOGIN, XOAUTH2
$config->from = 'usuario@test.com.br';
$config->fantasy = 'Test Ltda';
$config->replyTo = 'vendas@test.com.br';
$config->replyName = 'Vendas';
//$config->timeout = 30; //Quanto tempo aguardar a conexão para abrir, em segundos. O padrão de 5 minutos (300s) é da seção RFC2821 4.5.3.2 Isso precisa ser bem alto para funcionar corretamente com hosts usando greetdelay como medida anti-spam.
//$config->timelimit = 30; //Quanto tempo esperar pelos comandos para concluir, em segundos. O padrão de 5 minutos (300s) é da seção RFC2821 4.5.3.2

use NFePHP\Mail\Mail;

try {
    //paramtros:
    //config - (obrigatório) vide acima
    //xml - (obrigatório) documento a ser enviado NFe, NFCe, CTe, ou CCe, pode ser um path ou o arquivo em string
    //pdf - (opcional) documento a ser enviado DANFE, DANFCE, DACTE, ou DACCE, pode ser um path ou o arquivo em string
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
