# sped-mail

O envio de emails é parte integrante das necessidades de quem opera com os serviços SPED.

De acordo com a legislação é obrigatório o envio de emails contendo os xml das NFe e dos CTe aos seus repectivos destinatários.

Alguns destinatários, erroneamente, pedem também que seja enviado o PDF relativo ao Documento Auxiliar (DANFE, DACTE ou DACCE) em anexo a esse email.

Outros requerem que os emails seja enviados a várias caixas postais.

Esta parte da API se destina a prover essa facilidade, caso se deseje.

[![Join the chat at https://gitter.im/nfephp-org/sped-mail](https://badges.gitter.im/nfephp-org/sped-mail.svg)](https://gitter.im/nfephp-org/sped-mail?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Latest Stable Version][ico-stable]][link-github-releases]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![License][ico-license]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

[![Issues][ico-issues]][link-issues]
[![Forks][ico-forks]][link-forks]
[![Stars][ico-stars]][link-stars]


# Como instalar :

Adicione este pacote com o composer, por linha de comando:
```
composer require nfephp-org/sped-mail
```

Ou adicione ao seu composer.json:
```
{
    "require": {
    	"nfephp-org/sped-mail": "^0.1"
    }
}
```

# Como usar :

Essa classe pode ser usada de duas formas distintas.

## 1 - Usando o método estatico:
```php
$resp = Mail::sendMail($config, $xml, $pdf, $addresses, $template);
```
Onde :
$config é um stdClass contendo as configuração de seu SMTP (OBRIGATÓRIO)
```php
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
```
$xml é o path ou o conteudo do xml que se deseja enviar (OBRIGATÓRIO)
```php
$xml = '../nfe.xml';
```
ou ainda
```php
$xml = file_get_contents('../nfe.xml');
```
Idem para o $pdf (OPCIONAL)
```php
$pdf = '../nfe.pdf';
```
ou ainda
```php
$pdf = file_get_contents('../nfe.pdf');
```

$address é um array contendo os endereços de email para quem você deseja enviar a mensagem. 
Essas listas de email serão verificadas e os endereços que não forem validos serão descartados.
Se não for passada uma lista de endereços o sistema irá procurar no XML pelos endereços e esses serão usados, se existirem. (OPCIONAL)
```php
$addresses = ['fulano@client.com.br'];
```
O template usado pode ser substituido pelo de sua escolha, usando o parametro $template (OPCIONAL).
Use como referencia os templates padrões para criar o seu veja isso na classe Base.php
```php
$template = '<p>Meu HTML {emitente} .... ';
```

Para maiores detalhes veja os exemplos indicados na pasta examples.

> #### NOTA: Em caso de falha será retornado um EXCEPTION

# Como enviar para vários destinatários

Pordemos enviar os emails para vários destinatários basicamente de duas maneiras diferentes:

## 1 - Indicando todos os destinatários no próprio XML do documento
Neste caso podemos fazer uso da tag &lt;obsCont&gt; podem existir dezenas desses campos no xml, essa com certeza é a manira mais inteligente de indicar vários destinários, pois podem ser lidos diretamente do xml.

Veja que o tipo do campo xCampo="email" passa a ser obrigatório para que possamos identificar que este campo indica um email.

```xml
 <obsCont xCampo="email">
     <xTexto>fulano@yahoo.com.br</xTexto>
 </obsCont>
```


## 2 - Passando os endereços adicionais em um array nesta classe
Essa forma já foi indicada acima na variável $addresses = [ ... ];


[ico-stars]: https://img.shields.io/github/stars/nfephp-org/sped-mail.svg?style=flat-square
[ico-forks]: https://img.shields.io/github/forks/nfephp-org/sped-mail.svg?style=flat-square
[ico-issues]: https://img.shields.io/github/issues/nfephp-org/sped-mail.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/nfephp-org/sped-mail/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/nfephp-org/sped-mail.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/nfephp-org/sped-mail.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/nfephp-org/sped-mail.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/nfephp-org/sped-mail.svg?style=flat-square
[ico-stable]: https://poser.pugx.org/nfephp-org/sped-mail/v/stable.svg?style=flat-square
[ico-license]: https://poser.pugx.org/nfephp-org/nfephp/license.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/nfephp-org/sped-mail
[link-github-releases]: https://github.com/nfephp-org/sped-mail/releases
[link-travis]: https://travis-ci.org/nfephp-org/sped-mail
[link-scrutinizer]: https://scrutinizer-ci.com/g/nfephp-org/sped-mail/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/nfephp-org/sped-mail
[link-downloads]: https://packagist.org/packages/nfephp-org/sped-mail
[link-author]: https://github.com/nfephp-org
[link-issues]: https://github.com/nfephp-org/sped-mail/issues
[link-forks]: https://github.com/nfephp-org/sped-mail/network
[link-stars]: https://github.com/nfephp-org/sped-mail/stargazers

