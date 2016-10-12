<?php

namespace NFePHP\Mail\Tests;

use NFePHP\Mail\Mail;

class MailTest extends \PHPUnit_Framework_TestCase
{
    public $dummyMailer;
    public $config;
    
    public function __construct()
    {
        $this->config = new \stdClass();
        $this->config->host = 'smtp.test.com.br';
        $this->config->user = 'usuario@test.com.br';
        $this->config->password = 'senha';
        $this->config->secure = 'tls';
        $this->config->port = 587;
        $this->config->from = 'usuario@test.com.br';
        $this->config->fantasy = 'Test Ltda';
        $this->config->replyTo = 'vendas@test.com.br';
        $this->config->replyName = 'Vendas';
        
        $this->dummyMailer = $this->getMockBuilder('\PHPMailer')
            ->setMethods(['send'])    
            ->getMock();
    }
    
    public function testSetTemplate()
    {
        $this->assertTrue(true);
    }
    
    public function testLoadDocuments()
    {
        $this->assertTrue(true);
    }
    
    public function testLoadDocumentsFail()
    {
        $this->assertTrue(true);
    }
    
    public function testSend()
    {
        $this->assertTrue(true);
    }
    
    public function testSendFail()
    {
        $this->assertTrue(true);
    }
}
