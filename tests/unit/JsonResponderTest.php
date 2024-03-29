<?php
namespace tests;

use Germania\Responder\JsonResponder;
use Germania\Responder\ResponderInterface;
use Germania\Responder\ResponderInvalidArgumentException;
use Germania\Responder\ResponderExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm;

use Prophecy\PhpUnit\ProphecyTrait;

class JsonResponderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;


    public function testInstantiation()
    {
        $options = \JSON_PRETTY_PRINT;

        $sut = new JsonResponder($options);
        $this->assertInstanceOf(ResponderInterface::class, $sut);
        $this->assertIsCallable( $sut);

        return $sut;
    }


    /**
     * @depends testInstantiation
     */
    public function testResponseFactoryInterceptor( $sut )
    {
        $response_factory_mock = $this->prophesize( ResponseFactoryInterface::class );
        $response_factory = $response_factory_mock->reveal();

        $fluid = $sut->setResponseFactory( $response_factory);
        $this->assertSame($fluid, $sut);

        return $sut;
    }





    /**
     * @dataProvider provideJsonSerializableData
     * @depends testInstantiation
     */
    public function testResponseCreation($thingy, $status, $sut )
    {
        $sut->setResponseFactory( new Nyholm\Psr7\Factory\Psr17Factory);

        $response = $sut->createResponse( $thingy, $status );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($response->getStatusCode(), $status);

        $response = $sut( $thingy, $status );
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
        $sut->setResponseFactory( new Nyholm\Psr7\Factory\Psr17Factory);

        $this->expectException(ResponderExceptionInterface::class);
        $this->expectException(ResponderInvalidArgumentException::class);
        $sut->createResponse( $thingy );

        $this->expectException(ResponderExceptionInterface::class);
        $this->expectException(ResponderInvalidArgumentException::class);
        $sut( $thingy );
    }

    public function provideInvalidData()
    {
        return array(
            'resource type'   => [ tmpfile() ],
            'StdClass object' => [ new \StdClass ],
        );
    }




}
