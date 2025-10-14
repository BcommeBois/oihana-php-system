<?php

namespace oihana\controllers\traits;

use DI\NotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;

use oihana\enums\http\HttpParamStrategy;
use PHPUnit\Framework\TestCase;

final class GetParamTraitTest extends TestCase
{
    use GetParamTrait;

    protected function setUp(): void
    {
        // Initialize default strategy
        $this->paramsStrategy = HttpParamStrategy::BOTH ;
    }

    public function testGetBodyParamSimple()
    {
        $body = ['name' => 'Alice', 'age' => 30];

        $request = $this->createMock(Request::class);
        $request->method('getParsedBody')->willReturn($body);

        $this->assertSame('Alice', $this->getBodyParam($request, 'name'));
        $this->assertSame('30', $this->getBodyParam($request, 'age'));
        $this->assertNull($this->getBodyParam($request, 'missing'));
    }

    public function testGetBodyParamsMultiple()
    {
        $body = ['name' => 'Bob', 'geo' => ['lat' => 12.34, 'lng' => 56.78]];

        $request = $this->createMock(Request::class);
        $request->method('getParsedBody')->willReturn($body);

        $result = $this->getBodyParams($request, ['name', 'geo.lat', 'geo.lng', 'missing']) ;

        $this->assertSame
        ([
            'name' => 'Bob',
            'geo' => ['lat' => 12.34, 'lng' => 56.78]
        ]
        , $result ) ;
    }

    /**
     * @throws NotFoundException
     */
    public function testGetParamWithDefaultAndThrowable()
    {
        $query = ['foo' => 'bar'];

        $request = $this->createMock(Request::class);
        $request->method('getQueryParams' )->willReturn($query);
        $request->method('getParsedBody'  )->willReturn([]);

        // existing param
        $this->assertSame('bar', $this->getParam( $request, 'foo'));

        // missing param with default
        $this->assertSame('default', $this->getParam( $request, 'baz', ['baz' => 'default']));

        // missing param with throwable
        $this->expectException(NotFoundException::class);
        $this->getParam( $request , 'baz', [], true ) ;
    }

    /**
     * @throws NotFoundException
     */
    public function testGetParamIntAndFloat()
    {
        $body = [ 'int' => '42' , 'float' => '3.14' ] ;

        $request = $this->createMock(Request::class) ;
        $request->method('getParsedBody')->willReturn( $body ) ;
        $request->method('getQueryParams')->willReturn([]);

        $this->assertSame(42   , $this->getParamInt   ( $request , 'int'   ) ) ;
        $this->assertSame(3.14 , $this->getParamFloat ( $request , 'float' ) ) ;
    }

    /**
     * @throws NotFoundException
     */
    public function testGetParamNumberWithRange()
    {
        $body = ['number' => '50'];

        $request = $this->createMock(Request::class);
        $request->method('getParsedBody')->willReturn($body);

        $result = $this->getParamNumberWithRange($request, 'number', FILTER_VALIDATE_INT, 10, 100);
        $this->assertSame(50, $result);

        // test out of range returns null
        $result = $this->getParamNumberWithRange($request, 'number', FILTER_VALIDATE_INT, 60, 100);
        $this->assertNull($result);

        // test out of range returns defaultValue
        $result = $this->getParamNumberWithRange($request, 'number', FILTER_VALIDATE_INT, 60, 100 , 0 );
        $this->assertSame( 0 , $result);
    }

    public function testGetQueryParam()
    {
        $query = ['search' => 'test'];
        $request = $this->createMock(Request::class);
        $request->method('getQueryParams')->willReturn($query);

        $this->assertSame('test' , $this->getQueryParam( $request , 'search' ) ) ;
        $this->assertNull($this->getQueryParam( $request , 'missing' ) ) ;
    }

    public function testInitializeParamsStrategy()
    {
        $this->initializeParamsStrategy(HttpParamStrategy::BODY);
        $this->assertSame(HttpParamStrategy::BODY, $this->paramsStrategy);

        $this->initializeParamsStrategy(['paramsStrategy' => HttpParamStrategy::QUERY]);
        $this->assertSame(HttpParamStrategy::QUERY, $this->paramsStrategy);

        // invalid strategy should keep previous
        $this->initializeParamsStrategy('invalid');
        $this->assertSame(HttpParamStrategy::QUERY, $this->paramsStrategy);
    }

}