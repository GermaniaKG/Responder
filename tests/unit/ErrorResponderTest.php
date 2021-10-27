<?php
namespace tests;

use Germania\Responder\ErrorResponder;
use Germania\Responder\JsonResponder;
use Germania\Responder\ResponderInterface;
use Germania\Responder\ResponderInvalidArgumentException;
use Germania\Responder\ResponderExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use Prophecy\PhpUnit\ProphecyTrait;


class ErrorResponderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

    public $inner_responder;

    public function setUp() : void
    {
        parent::setUp();

        $this->inner_responder = new JsonResponder(\JSON_PRETTY_PRINT);

    }

    public function testInstantiation()
    {
        $debug = false;
        $sut = new ErrorResponder($debug, $this->inner_responder);
        $this->assertInstanceOf(ResponderInterface::class, $sut);
        $this->assertIsCallable( $sut);

        return $sut;
    }


    /**
     * @dataProvider provideJsonSerializableData
     * @depends testInstantiation
     */
    public function testResponseCreation($thingy, $debug, $status, $sut )
    {
        $sut->setDebug($debug);

        $response = $sut( $thingy, $status );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($response->getStatusCode(), $status);

        $response = $sut->createResponse( $thingy, $status );
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($response->getStatusCode(), $status);

        $response_body = (string) $response->getBody();

        print_r($response_body); echo PHP_EOL;

        $response_body_decoded = json_decode($response_body, "force_array");
        $this->assertIsArray($response_body_decoded['errors']);
    }


    public function provideJsonSerializableData()
    {

        $E1 = new \RuntimeException("Boo!");
        return array(
            'RuntimeException, debug ON and status 500' => [ $E1, true, 500],
            'Exception with previous RuntimeException, debug ON and status 500' => [ new \Exception("Outer", 0, $E1), true, 500 ],
            'RuntimeException, debug OFF and status 500' => [ $E1, false, 500],
            'Exception with previous RuntimeException, debug OFF and status 501' => [ new \Exception("Outer", 0, $E1), false, 501 ],
        );
    }



    /**
     * @dataProvider provideInvalidData
     * @depends testInstantiation
     */
    public function testExceptions($thingy, $sut )
    {
        $this->expectException(ResponderExceptionInterface::class);
        $this->expectException(ResponderInvalidArgumentException::class);
        $sut( $thingy );

        $this->expectException(ResponderExceptionInterface::class);
        $this->expectException(ResponderInvalidArgumentException::class);
        $sut->createResponse( $thingy );
    }

    public function provideInvalidData()
    {
        return array(
            'Just a string' => [ "foo" ]
        );
    }




}
