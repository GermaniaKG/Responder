<img src="https://static.germania-kg.com/logos/ga-logo-2016-web.svgz" width="250px">

------



# Germania KG · Responder

[![Packagist](https://img.shields.io/packagist/v/germania-kg/responder.svg?style=flat)](https://packagist.org/packages/germania-kg/responder)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/responder.svg)](https://packagist.org/packages/germania-kg/responder)
[![Build Status](https://img.shields.io/travis/GermaniaKG/Responder.svg?label=Travis%20CI)](https://travis-ci.org/GermaniaKG/Responder)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/Responder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Responder/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/Responder/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Responder/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/Responder/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Responder/build-status/master)



## Installation

```bash
$ composer require germania-kg/responder
```



## Responder classes

- [TwigResponder](#TwigResponder)
- [JsonResponder](#JsonResponder)
- [ErrorResponder](#ErrorResponder)



------



## Interfaces

### **ResponderInterface**

The `Germania\Responder\ResponderInterface` provides a **createResponse** method which accepts data to create a PSR-7 responde from. The data can be of any type. 

Implementing classes must also be *callable* and implement an __invoke__ function with same signature.

- Should throw *ResponderInvalidArgumentException* when passed data is incorrect.
- Should throw *ResponderRuntimeException* when s.th. bad happens underway.

```php
public function createResponse( $data ) : ResponseInterface;
public function __invoke( $data ) : ResponseInterface;
```



### ResponderExceptionInterface

The `Germania\Responder\ResponderExceptionInterface` is the base interface all Responder exceptions have in common. See [Exceptions](#Exceptions) section.  



------



## TwigResponder

The constructor accepts a *Twig Environment*, the name of the array field which holds the *template,* and optionally a default *context variables* array. 

You can optionally pass a custom *PSR-17 Response Factory*, per default the *Response factory* from **[slim/psr7](https://packagist.org/packages/slim/psr7)** will be used.

The template `$data` passed to *createResponse* method will be merged with `$default_context`.

### Setup

```php
<?php
use Germania\Responder\TwigResponder;

// Have Twig\Environment at hand
$twig = ...;
$responder = new TwigResponder($twig, "template");

// These are optional
$default_context = array();
$psr17 = new \Nyholm\Psr7\Factory\Psr17Factory;
$responder = new TwigResponder($twig, "template", $default_context, $psr17);
```

### Configuration

```php
$responder->setTwig( $twig );
$responder->setTemplateField('template');
$responder->setDefaultContext( array('another' => 'data') );
$responder->setResponseFactory($psr17);

# Fallback when context lacks 'template' element
$responder->setDefaultTemplate('website.tpl');
```

### Usage

```php
$data = array(
	'template' => 'website.tpl',
  'foo' => 'bar'
)

// These are equal:
$response = $responder->createResponse($data);
$response = $responder($data);
```



------



## JsonResponder

Creates a JSON response from the given data. Implements *ResponderInterface*. 
Responses will have  `Content-type: application/json`. 

You can optionally pass a custom *PSR-17 Response Factory*, per default the *Response factory* from **[slim/psr7](https://packagist.org/packages/slim/psr7)** will be used.

### Setup

```php
<?php
use Germania\Responder\JsonResponder;
use Germania\Responder\ResponderExceptionInterface;
use Slim\Psr7\Factory\ResponseFactory;

$json = \JSON_PRETTY_PRINT;
$psr17 = new ResponseFactory; // Optional

$responder = new JsonResponder($json);
$responder = new JsonResponder($json, $psr17);
```

### Configuration

```php
$responder->setJsonOptions( \JSON_PRETTY_PRINT );
$responder->setResponseContentType('application/json');
$responder->setResponseFactory($psr17);
```

### Usage

```php
try {
  $data = array('foo' => 'bar');
  
  // These
  $response = $responder->createResponse($data);
  $response = $responder($data);

  // Psr\Http\Message\ResponseInterface  
  return $response;
}
catch(ResponderExceptionInterface $e) {
  echo $e->getMessage();
}
```



------



## ErrorResponder

The **ErrorResponder** mangles *Throwables* and acts as decorator for another *ResponderInterface*. It extends from *ResponderDecoratorAbstract* and implements *ResponderInterface*.

The error passed to *createResponse* is converted to an array with an `errors` element that contains the error and all its previous errors (depending on `debug` mode). The array will then be passed to the inner responder.

The default response status code is `500` and can be adjusted on call.

Extends from ***ResponderDecoratorAbstract*** and implements *ResponderInterface*.

### Setup

```php
<?php
use Germania\Responder\ErrorResponder;
use Germania\Responder\JsonResponder;

$debug = true;
$inner_responder = new JsonResponder(\JSON_PRETTY_PRINT);

$error_responder = new ErrorResponder($debug, $inner_responder);
```

### Configuration

```php
$responder->setDebug( false );
```

### Usage

Optionally pass a custom status code; default is `500`.

```php
try {
  // Throw something here
}
catch(\Throwable $e) {
  $response = $error_responder->createResponse($e);
  echo $response->getStatusCode(); // 500
  
  $response = $error_responder->createResponse($e, 503);  
  $response = $error_responder->createResponse($e, 400);

	// Psr\Http\Message\ResponseInterface    
  return $response;
}
```

```php
$responder->setDebug( false );
```



### Response Examples

These examples assume a `JsonResponder` was used as inner responder. Note the `errors` element: it contains the error object, and optionally, its previous errors.

```json
{
  "errors": [
    {
      "type": "RuntimeException",
      "message": "Boo!",
      "code": 0
    }
  ]
}
```

When **debug** is `TRUE`, previous exceptions are included, and the location of the occurrence:

```json
{
  "errors": [
    {
      "type": "Exception",
      "message": "Outer",
      "code": 0,
      "location": "\/path\/to\/file.php:67"
    },
    {
      "type": "MyLibrary\/CustomException",
      "message": "Boo!",
      "code": 0,
      "location": "\/path\/to\/file.php:64"
    }
  ]
}
```



------



## Exceptions

- **Germania\Responder\ResponderExceptionInterface**
- **Germania\Responder\ResponderInvalidArgumentException**
  extends ***\InvalidArgumentException*** and implements *ResponderExceptionInterface.*
- **Germania\Responder\ResponderRuntimeException**
  extends ***\RuntimeException*** and implements *ResponderExceptionInterface.*



#### Example: Deal with errors

```php
<?php
use Germania\Responder\ResponderInvalidArgumentException;
use Germania\Responder\ResponderRuntimeException;
use Germania\Responder\ResponderExceptionInterface;

try {
  $data = array('foo' => 'bar');
  
  // These are equal:
  $response = $responder->createResponse($data);
  $response = $responder($data);

  // Psr\Http\Message\ResponseInterface  
  return $response;
}
catch(ResponderInvalidArgumentException $e) {
  // $data has been invalid
}
catch(ResponderRuntimeException $e) {
  // Something bad happened
}
catch(ResponderExceptionInterface $e) {
  // Catch any other Responder exceptions
}
```





------



## Traits

### ResponderTrait

Use the `Germania\Responder\ResponderTrait` in your classes:

```php
// @var ResponderInterface
protected $responder;

// @return ResponderInterface|null
public function getResponder() : ?ResponderInterface;

// @param ResponderInterface $responder
// @return static
public function setResponder( ResponderInterface $responder );
```



### ResponseFactoryTrait

*TwigResponder* and *JsonResponder* use the `Germania\Responder\ResponseFactoryTrait`. Per default they use the Response factory from **[yholm/psr7](https://packagist.org/packages/nyholm/psr7).**

```php
// @var ResponseFactory
public $response_factory;

// @param ResponseFactoryInterface $response_factory
// @return static
public function setResponseFactory(ResponseFactoryInterface $response_factory ) : static

// @return ResponseFactoryInterface|null  
public function getResponseFactory() : ?ResponseFactoryInterface;
```



------



## Development

```bash
$ git clone git@github.com:GermaniaKG/Responder.git
$ cd Responder
$ composer install
```

