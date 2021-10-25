<?php
namespace tests;

use Germania\Responder\JsonResponder;
use Germania\Responder\CallbackResponder;
use Germania\Responder\ResponderInterface;
use Germania\Responder\ResponderInvalidArgumentException;
use Germania\Responder\ResponderExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm;

use Prophecy\PhpUnit\ProphecyTrait;

class CallbackResponderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;


    public function testInstantiation()
    {
        $callback = function ($data) { return $data; };
        $inner_responder = new JsonResponder;

        $sut = new CallbackResponder($callback, $inner_responder);
        $this->assertInstanceOf(ResponderInterface::class, $sut);
        $this->assertIsCallable( $sut);

        return $sut;
    }


    /**
     * @depends testInstantiation
     */
    public function testCallbackInterceptors( $sut )
    {
        $callback = function ($data) { return print_r($data, "noecho"); };

        $fluid = $sut->setCallback( $callback);
        $this->assertSame($fluid, $sut);
        $this->assertSame($sut->getCallback(), $callback);
    }





    /**
     * @dataProvider provideJsonSerializableData
     * @depends testInstantiation
     */
    public function testResponseCreation($thingy, $status, $sut )
    {
        $callback = function ($data) { return $data; };
        $sut->setCallback( $callback );
        $response = $sut->createResponse( $thingy, $status );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($response->getStatusCode(), $status);
    }

    public function provideJsonSerializableData()
    {
        return array(
            'String value with status 200' => [ "foo", 200 ],
            'Array with status 301'        => [ array("foo"   => "bar"), 301 ],
            'Boolean FALSE status 400'     => [ false, 400 ],
            'Boolean TRUE with status 200' => [ true, 200 ]
        );
    }



    /**
     * @dataProvider provideInvalidData
     * @depends testInstantiation
     */
    public function testExceptions($thingy, $sut )
    {
        $callback = function ($data) { return $data; };
        $sut->setCallback( $callback );

        $this->expectException(ResponderExceptionInterface::class);
        $this->expectException(ResponderInvalidArgumentException::class);
        $sut->createResponse( $thingy );
    }

    public function provideInvalidData()
    {
        return array(
            'resource type'   => [ tmpfile() ],
            'StdClass object' => [ new \StdClass ],
        );
    }




}
