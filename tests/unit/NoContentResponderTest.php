<?php
namespace tests;

use Germania\Responder\NoContentResponder;
use Germania\Responder\ResponderInterface;
use Germania\Responder\ResponderInvalidArgumentException;
use Germania\Responder\ResponderExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm;

use Prophecy\PhpUnit\ProphecyTrait;

class NoContentResponderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;


    public function testInstantiation()
    {
        $sut = new NoContentResponder();
        $this->assertInstanceOf(ResponderInterface::class, $sut);
        $this->assertIsCallable( $sut);

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
        $this->assertEmpty($response->getBody()->__toString());
        $this->assertEquals($response->getStatusCode(), $status);

        $response = $sut( $thingy, $status );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEmpty($response->getBody()->__toString());
        $this->assertEquals($response->getStatusCode(), $status);
    }

    public function provideJsonSerializableData()
    {
        return array(
            'String value with status 200' => [ "foo", 204 ],
            'Array with status 301'        => [ array("foo"   => "bar"), 204 ],
            'Boolean FALSE status 400'     => [ false, 204 ],
            'Boolean TRUE with status 200' => [ true,  204 ]
        );
    }




}
