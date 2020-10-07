# Germania KG · Responder



## Installation

```bash
$ composer require germania-kg/responder
```



## Usage



### Interfaces

**Germania\Responder\ResponderInterface**

Should throw *ResponderInvalidArgumentException* when passed data is incorrect.

Should throw *ResponderRuntimeException* when s.th. bad happens underway.

```php
public function createResponse( $result ) : ResponseInterface;
```





### TwigResponder

The constructor accepts a *Twig Environment*, the name of the array field which holds the *template,* and optionally a default *context variables* array.

The template `$data` passed to *createResponse* method will be merged with `$default_context`.

```php
<?php
use Germania\Responder\TwigResponder;

// Have Twig\Environment at hand
$twig = ...;
$default_context = array();

$responder = new TwigResponder($twig, "template", $default_context);

$data = array(
	'template' => 'website.tpl',
  'foo' => 'bar'
);
$response = $responder->createResponse($data);
```

#### Configuration

```php
$responder->setTwig( $twig );
$responder->setTemplateField('template');
$responder->setDefaultContext( array('another' => 'data') );
```



### JsonResponder

Creates a JSON response from the given data. Implements *ResponderInterface*.

```php
<?php
use Germania\Responder\JsonResponder;
use Germania\Responder\ResponderExceptionInterface;
use Slim\Psr7\Factory\ResponseFactory;

$json = \JSON_PRETTY_PRINT;
$psr17 = new ResponseFactory; // Optional

$responder = new JsonResponder($json);
$responder = new JsonResponder($json, $psr17);

try {
  $data = array('foo' => 'bar');
  $response = $responder->createResponse($data);
  // Psr\Http\Message\ResponseInterface  
  
  return $response;
}
catch(ResponderExceptionInterface $e) {
  echo $e->getMessage();
}
```

#### Configuration

```php
$responder->setJsonOptions( \JSON_PRETTY_PRINT );
$responder->setResponseContentType('application/json');
```





### ErrorResponder

Decorator for another *ResponderInterface* which mangles *Throwables*. The response status code is `500` and can be adjusted on call.

Extends from ***ResponderDecoratorAbstract*** and implements *ResponderInterface*.

```php
<?php
use Germania\Responder\ErrorResponder;
use Germania\Responder\JsonResponder;

$debug = true;
$inner = new JsonResponder(\JSON_PRETTY_PRINT);

$error_responder = new ErrorResponder($debug, $inner);

try {
  // Throw something here
}
catch(\Throwable $e) {
  $response = $error_responder->createResponse($e);
  $response = $error_responder->createResponse($e, 503);  
  $response = $error_responder->createResponse($e, 400);
  // Psr\Http\Message\ResponseInterface  
  
  return $response;
}

```

#### Configuration

```php
$responder->setDebug( false );
```



#### Responses

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



### Exceptions

**Germania\Responder\ResponderExceptionInterface**

**Germania\Responder\ResponderInvalidArgumentException**
extends *\InvalidArgumentException* and implements *ResponderExceptionInterface.*

**Germania\Responder\ResponderRuntimeException**
extends *\RuntimeException* and implements *ResponderExceptionInterface.*



### Traits

**Germania\Responder\ResponderTrait**

```php
// @var ResponderInterface
protected $responder;

// @return ResponderInterface|null
public function getResponder() : ?ResponderInterface;

// @param ResponderInterface $responder
// @return static
public function setResponder( ResponderInterface $responder );
```



**Germania\Responder\ResponseFactoryTrait**

*TwigResponder* and *JsonResponder* use this trait, and per default they use `Slim\Psr7\Factory\ResponseFactory`. It can be changed like this:

```php
// @var ResponseFactory
public $response_factory;

// @param ResponseFactoryInterface $response_factory
// @return static
public function setResponseFactory(ResponseFactoryInterface $response_factory ) : static

// @return ResponseFactoryInterface|null  
public function getResponseFactory() : ?ResponseFactoryInterface;
```



## Development

```bash
$ git clone git@github.com:GermaniaKG/Responder.git
$ cd Responder
$ composer install
```

