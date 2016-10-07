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

//$mail = new Mail($config);

$resp = Mail::sendMail($config, 'nfe.xml', '', ['recebedor@outro.com.br'], '');