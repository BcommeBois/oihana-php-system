<?php

namespace tests\oihana\routes\traits;

use DI\Container;
use PHPUnit\Framework\TestCase;
use oihana\routes\traits\HttpMethodRoutesTrait;
use InvalidArgumentException;

class HttpMethodRoutesTraitTest extends TestCase
{
    private object $mock;

    protected function setUp(): void
    {
        $this->mock = new class {
            use HttpMethodRoutesTrait;

            public Container $container;
          };
    }

    public function testInitializeMethodsSetsProperties()
    {
        $init = [
            'delete' => 'deleteMethod',
            'get'    => 'getMethod',
            'list'   => 'listMethod',
            'patch'  => 'patchMethod',
            'post'   => 'postMethod',
            'put'    => 'putMethod',
        ];

        $this->mock->initializeMethods($init);

        $this->assertSame('deleteMethod', $this->mock->delete);
        $this->assertSame('getMethod', $this->mock->get);
        $this->assertSame('listMethod', $this->mock->list);
        $this->assertSame('patchMethod', $this->mock->patch);
        $this->assertSame('postMethod', $this->mock->post);
        $this->assertSame('putMethod', $this->mock->put);
    }

    public function testMethodThrowsExceptionForInvalidClass()
    {
        $this->expectException(InvalidArgumentException::class);
        $routes = [];
        $this->mock->method('NonExistentClass', $routes, '/path', 'METHOD');
    }
}