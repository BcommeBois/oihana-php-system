<?php

namespace oihana\controllers\traits;

use oihana\enums\JsonParam;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use oihana\controllers\enums\ControllerParam;
use oihana\enums\http\HttpHeader;
use oihana\files\enums\FileMimeType;

use PHPUnit\Framework\TestCase;

final class JsonTraitTest extends TestCase
{
    private object $mock;

    protected function setUp(): void
    {
        $this->mock = new class {
            use JsonTrait;
        };
    }

    public function testInitializeJsonOptionsFromInit()
    {
        $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;

        $result = $this->mock->initializeJsonOptions([
            ControllerParam::JSON_OPTIONS => $flags
        ]);

        $this->assertSame($this->mock, $result);
        $this->assertSame($flags, $this->mock->jsonOptions);
    }

    public function testInitializeJsonOptionsFromContainer()
    {
        $flags = JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT;

        $container = $this->createStub(ContainerInterface::class ) ;
        $container->method('has')->with(ControllerParam::JSON_OPTIONS)->willReturn(true);
        $container->method('get')->with(ControllerParam::JSON_OPTIONS)->willReturn($flags);

        $this->mock->initializeJsonOptions([], $container);

        $this->assertSame($flags, $this->mock->jsonOptions);
    }

    public function testInitializeJsonOptionsInvalidFlags()
    {
        $invalidFlags = 0xFFFF; // non valides
        $this->mock->initializeJsonOptions([ControllerParam::JSON_OPTIONS => $invalidFlags]);

        $this->assertSame(JsonParam::JSON_NONE, $this->mock->jsonOptions);
    }

    public function testInitializeJsonOptionsFallbackToNone()
    {
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->with(ControllerParam::JSON_OPTIONS)->willReturn(false);

        $this->mock->initializeJsonOptions([], $container);

        $this->assertSame(JsonParam::JSON_NONE, $this->mock->jsonOptions);
    }

    public function testJsonResponseWritesJsonAndSetsHeaders()
    {
        $data = ['foo' => 'bar'];

        $stream = $this->createStub(StreamInterface::class);
        $stream->method('write')
            ->with(json_encode($data, $this->mock->jsonOptions));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $response->expects($this->once())
            ->method('withStatus')
            ->with(201)
            ->willReturnSelf();

        $response->expects($this->once())
            ->method('withHeader')
            ->with(HttpHeader::CONTENT_TYPE, FileMimeType::JSON)
            ->willReturnSelf();

        $result = $this->mock->jsonResponse($response, $data, 201);

        $this->assertSame($response, $result);
    }

}