<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

$config = new stdClass();
$config->mail->host = 'smtp.test.com.br';
$config->mail->user = 'usuario@test.com.br';
$config->mail->password = 'senha';
$config->mail->secure = 'tls';
$config->mail->port = 587;
$config->mail->from = 'usuario@test.com.br';
$config->mail->fantasy = 'Test Ltda';
$config->mail->replyTo = 'vendas@test.com.br';
$config->mail->replyName = 'Vendas';

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
