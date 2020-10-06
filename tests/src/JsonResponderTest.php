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
        $options = null;

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
    public function testResponseCreation($thingy, $sut )
    {
        $sut->setResponseFactory( new ResponseFactory);

        $response = $sut->createResponse( $thingy );

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function provideJsonSerializableData()
    {
        return array(
            [ "foo" ],
            [ array("foo" => "bar") ],
            [ false ],
            [ true ]
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
