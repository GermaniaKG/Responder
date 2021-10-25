<?php
namespace tests;

use Germania\Responder\RouteNameRedirectResponder;
use Germania\Responder\ResponderInterface;
use Germania\Responder\ResponderInvalidArgumentException;
use Germania\Responder\ResponderExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm;

use Prophecy\PhpUnit\ProphecyTrait;

class RouteNameRedirectResponderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;


    public function testInstantiation()
    {
        $uri_creator = function($route, $data) { return true; };
        $sut = new RouteNameRedirectResponder("index", $uri_creator);
        $this->assertInstanceOf(ResponderInterface::class, $sut);
        $this->assertIsCallable( $sut);

        return $sut;
    }


    /**
     * @dataProvider provideSomeData
     * @depends testInstantiation
     */
    public function testResponseCreation($thingy, $status, $sut )
    {
        $sut->setResponseFactory( new Nyholm\Psr7\Factory\Psr17Factory);
        $uri_creator = function($route, $data) { return "/index"; };
        $sut->setUriCreator($uri_creator);

        $response = $sut->createResponse( $thingy, $status );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($response->getStatusCode(), $status);

        $location = $response->getHeaderLine('Location');
        $this->assertEquals($location, "/index");
    }

    public function provideSomeData()
    {
        return array(
            'Some value with expected 301' => [ array("foo"   => "bar"), 301 ],
            'Some value with expected 303' => [ array("foo"   => "bar"), 303 ],
        );
    }




}
