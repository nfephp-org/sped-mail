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
    //a configuração é uma stdClass com os campos acima indicados
    //esse parametro é OBRIGATÓRIO
    $mail = new Mail($config);
    
    //use isso para inserir seu próprio template HTML com os campos corretos 
    //para serem substituidos em execução com os dados dos xml
    $htmlTemplate = '';
    $mail->setTemplate($htmlTemplate);

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
    
} catch (InvalidArgumentException $e) {
    echo "Falha: " . $e->getMessage();
} catch (RuntimeException $e) {
    echo "Falha: " . $e->getMessage();
}  



