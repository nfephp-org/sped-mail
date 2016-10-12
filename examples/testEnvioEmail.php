<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

$config = new stdClass();
$config->host = 'email-ssl.com.br';
$config->user = 'roberto@fimatec.com.br';
$config->password = 'Q!w2e3R$';
$config->secure = 'tls';
$config->port = 587;
$config->from = 'roberto@fimatec.com.br';
$config->fantasy = 'Fimatec Ltda';
$config->replyTo = 'roberto@fimatec.com.br';
$config->replyName = 'Roberto';

use NFePHP\Mail\Mail;

$mail = new Mail($config);
$htmlTemplate = ''; //use isso para inserir seu próprio template HTML com os campos corretos para serem substituidos
$mail->setTemplate($htmlTemplate);
$mail->send($address);

