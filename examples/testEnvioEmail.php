<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

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

use NFePHP\Mail\Mail;

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
    
    //envia emails, se false apenas para os endereçospassados
    //se true para todos os endereços contidos no XML e mais os indicados adicionais
    $mail->send($addresses, true);
    
} catch (\InvalidArgumentException $e) {
    echo "Falha: " . $e->getMessage();
} catch (\RuntimeException $e) {
    echo "Falha: " . $e->getMessage();
} catch (\Exception $e) {
    echo "Falha: " . $e->getMessage();
}  



