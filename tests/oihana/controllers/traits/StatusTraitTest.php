<?php

namespace tests\oihana\controllers\traits;

use PHPUnit\Framework\TestCase;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

use oihana\controllers\traits\StatusTrait;
use oihana\enums\Output;

final class StatusTraitTest extends TestCase
{
    private object $mock;
    private ResponseInterface $response;
    private StreamInterface $stream;

    protected function setUp(): void
    {
        $this->mock = new class
        {
            use StatusTrait;

            public function getCurrentPath(?ServerRequestInterface $request = null, array $params = []): string
            {
                return '/current/path';
            }
        };

        $this->stream = $this->createMock(StreamInterface::class);
        $this->stream->method('write')->willReturnCallback(fn($data) => strlen((string)$data));

        $this->response = $this->createMock(ResponseInterface::class);
        $this->response->method('getBody')->willReturn($this->stream);
        $this->response->method('withStatus')->willReturnSelf();
        $this->response->method('withHeader')->willReturnSelf();
    }

    public function testFailReturnsResponseWithDefaultStatus()
    {
        $response = $this->mock->fail($this->response);

        $this->assertSame($this->response, $response);
    }

    public function testFailReturnsResponseWithCustomCodeAndDetails()
    {
        $response = $this->mock->fail(
            $this->response,
            406,
            'Validation failed',
            ['field' => 'required']
        );

        $this->assertSame($this->response, $response);
    }

    public function testStatusReturnsResponseWithMessageAndCode()
    {
        $response = $this->mock->status($this->response, 'Bad request', 400);

        $this->assertSame($this->response, $response);
    }

    public function testStatusReturnsNullWhenResponseIsNull()
    {
        $result = $this->mock->status(null, 'Any message', 200);
        $this->assertNull($result);
    }

    public function testSuccessReturnsResponseWithDataAndDefaults()
    {
        $data = ['foo' => 'bar'];

        $response = $this->mock->success(null, $this->response, $data);

        $this->assertSame($this->response, $response);
    }

    public function testSuccessReturnsDataDirectlyWhenResponseIsNull()
    {
        $data = ['foo' => 'bar'];
        $result = $this->mock->success(null, null, $data);

        $this->assertSame($data, $result);
    }

    public function testSuccessMergesInitOptionsIntoResponse()
    {
        $data = ['item' => 123];
        $init =
        [
            Output::COUNT    => 10,
            Output::LIMIT    => 5,
            Output::OFFSET   => 2,
            Output::STATUS   => 201,
            Output::URL      => '/custom/url',
            Output::OWNER    => ['id' => 1],
            Output::POSITION => 0,
            Output::TOTAL    => 50,
            Output::OPTIONS  => ['extra' => 'value'],
            Output::PARAMS   => ['a' => 1]
        ];

        $response = $this->mock->success(null, $this->response, $data, $init);

        $this->assertSame($this->response, $response);
    }
}