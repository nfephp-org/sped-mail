<?php

namespace NFePHP\Mail\Tests;

use NFePHP\Mail\Mail;

class MailTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURES = '/fixtures';
    public $dummyMailer;
    public $config;
    public $mail;
    
    /**
     * @covers NFePHP\Mail::__construct
     * @covers NFePHP\Mail::loadservice
     */
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
        $this->dummyMailer->method('send')->willReturn(true);
        $this->mail = new Mail($this->config, $this->dummyMailer);
    }
    
    public function testShouldInstantiate()
    {
        $this->assertInstanceOf('\NFePHP\Mail\Mail', $this->mail);
    }
    
    public function testLoadTemplate()
    {
        $expected = '<p>Teste</p>';
        $this->mail->loadTemplate($expected);
        $this->assertEquals($expected, $this->mail->template);
    }
    /**
     * @covers NFePHP\Mail::getXmlData
     */
    public function testLoadDocuments()
    {
        $expected = file_get_contents(__DIR__ . self::FIXTURES.DIRECTORY_SEPARATOR.'nfe.xml');
        $this->mail->loadDocuments($expected);
        $this->assertEquals($expected, $this->mail->xml);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoadDocumentsFail()
    {
        $expected = file_get_contents(__DIR__ . self::FIXTURES.DIRECTORY_SEPARATOR.'response.xml');
        $this->mail->loadDocuments($expected);
    }
    
    /**
     * @covers NFePHP\Base::<protected>
     * @covers NFePHP\Mail::renderTemplate
     * @covers NFePHP\Mail::render
     * @covers NFePHP\Mail::removeInvalidAdresses
     * @covers NFePHP\Mail::attach
     * @covers NFePHP\Mail::checkEmailAddress
     * @covers NFePHP\Mail::clearAddressString
     */
    public function testSend()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testSendFailNoValidAddress()
    {
        $this->mail->send(['ROBERTO#fail', 'testefail.com.br']);
    }
}
