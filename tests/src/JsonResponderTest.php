<?php
namespace tests;

use Germania\Responder\JsonResponder;
use Germania\Responder\ResponderInterface;
use Germania\Responder\ResponderInvalidArgumentException;
use Germania\Responder\ResponderExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ResponseFactory;

use Prophecy\PhpUnit\ProphecyTrait;

class JsonResponderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;


    public function testInstantiation()
    {
        $options = \JSON_PRETTY_PRINT;

        $response_factory_mock = $this->prophesize( ResponseFactoryInterface::class );
        $response_factory = $response_factory_mock->reveal();

        $sut = new JsonResponder($options, $response_factory);
        $this->assertInstanceOf(ResponderInterface::class, $sut);

        return $sut;
    }


    /**
     * @dataProvider provideJsonSerializableData
     * @depends testInstantiation
     */
    public function testResponseCreation($thingy, $status, $sut )
    {
        $sut->setResponseFactory( new ResponseFactory);

        $response = $sut->createResponse( $thingy, $status );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($response->getStatusCode(), $status);
    }

    public function provideJsonSerializableData()
    {
        return array(
            [ "foo", 200 ],
            [ array("foo" => "bar"), 301 ],
            [ false, 400 ],
            [ true, 200 ]
        );
    }



    /**
     * @dataProvider provideInvalidData
     * @depends testInstantiation
     */
    public function testExceptions($thingy, $sut )
    {
        $sut->setResponseFactory( new ResponseFactory);

        $this->expectException(ResponderExceptionInterface::class);
        $this->expectException(ResponderInvalidArgumentException::class);
        $sut->createResponse( $thingy );
    }

    public function provideInvalidData()
    {
        return array(
            [ tmpfile() ],
            [ new \StdClass ],
        );
    }




}
