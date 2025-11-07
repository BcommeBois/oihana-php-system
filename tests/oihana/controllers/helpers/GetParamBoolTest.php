<?php

namespace oihana\controllers\helpers ;

use DI\NotFoundException;
use oihana\enums\Boolean;
use oihana\enums\http\HttpParamStrategy;
use PHPUnit\Framework\TestCase;

use Psr\Http\Message\ServerRequestInterface;

final class GetParamBoolTest extends TestCase
{
    private function createRequest(array $query = [], array $body = []): ServerRequestInterface
    {
        $mock = $this->createMock(ServerRequestInterface::class);
        $mock->method('getQueryParams')->willReturn($query);
        $mock->method('getParsedBody')->willReturn($body);
        return $mock;
    }

    /**
     * @throws NotFoundException
     */
    public function testReturnsTrueForBooleanTrue()
    {
        $request = $this->createRequest(['active' => Boolean::TRUE]);
        $this->assertTrue(getParamBool($request, 'active'));
    }

    /**
     * @throws NotFoundException
     */
    public function testReturnsFalseForBooleanFalse()
    {
        $request = $this->createRequest(['active' => Boolean::FALSE]);
        $this->assertFalse(getParamBool($request, 'active'));
    }

    /**
     * @throws NotFoundException
     */
    public function testReturnsDefaultForMissingKey()
    {
        $request = $this->createRequest([]);
        $this->assertTrue(getParamBool($request, 'active', [], true));
        $this->assertFalse(getParamBool($request, 'active', [], false));
        $this->assertNull(getParamBool($request, 'active'));
    }

    public function testThrowsNotFoundExceptionWhenThrowable()
    {
        $this->expectException(NotFoundException::class);
        $request = $this->createRequest([]);
        getParamBool($request, 'missing', [], null, HttpParamStrategy::BOTH, true);
    }

    /**
     * @throws NotFoundException
     */
    public function testSearchesQueryWhenStrategyQuery()
    {
        $request = $this->createRequest(['flag' => Boolean::TRUE], ['flag' => Boolean::FALSE]);
        $this->assertTrue(getParamBool($request, 'flag', [], null, HttpParamStrategy::QUERY));
    }

    /**
     * @throws NotFoundException
     */
    public function testSearchesBodyWhenStrategyBody()
    {
        $request = $this->createRequest(['flag' => Boolean::FALSE], ['flag' => Boolean::TRUE]);
        $this->assertTrue(getParamBool($request, 'flag', [], null, HttpParamStrategy::BODY));
    }

    /**
     * @throws NotFoundException
     */
    public function testReturnsDefaultIfValueIsNotBoolean()
    {
        $request = $this->createRequest(['flag' => 'yes']);
        $this->assertTrue(getParamBool($request, 'flag', [], true));
        $this->assertFalse(getParamBool($request, 'flag', [], false));
        $this->assertNull(getParamBool($request, 'flag'));
    }

    /**
     * @throws NotFoundException
     */
    public function testReturnsNullWhenRequestIsNull()
    {
        $this->assertNull(getParamBool(null, 'any'));
        $this->assertTrue(getParamBool(null, 'any', [], true));
        $this->assertFalse(getParamBool(null, 'any', [], false));
    }
}