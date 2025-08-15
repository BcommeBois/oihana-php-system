<?php

namespace tests\oihana\traits ;

use InvalidArgumentException;
use oihana\enums\Char;
use oihana\traits\UriTrait;
use PHPUnit\Framework\TestCase;

class UriTraitTest extends TestCase
{
    private mixed $obj;

    protected function setUp(): void
    {
        $this->obj = new class
        {
            use UriTrait;
        };
    }

    public function testBuildUriSimple()
    {
        $uri = 'https://example.com/path';
        $result = $this->obj->buildUri($uri);
        $this->assertSame($uri, $result);
    }

    public function testBuildUriWithQueryMerge()
    {
        $uri = 'https://example.com/path?foo=1';
        $options = ['query' => ['bar' => '2']];
        $expected = 'https://example.com/path?foo=1&bar=2';
        $result = $this->obj->buildUri($uri, $options);
        $this->assertSame($expected, $result);
    }

    public function testBuildUriWithFragmentReplace()
    {
        $uri = 'https://example.com/path#section';
        $options = ['fragment' => ['tab' => 'details']];
        $expected = 'https://example.com/path#tab=details';
        $result = $this->obj->buildUri($uri, $options);
        $this->assertSame($expected, $result);
    }

    public function testBuildUriWithUserPassPort()
    {
        $uri = 'https://user:pass@example.com:8080/path';
        $result = $this->obj->buildUri($uri);
        $this->assertStringStartsWith('https://user:pass@example.com:8080/path', $result);
    }

    public function testBuildUriInvalidUriThrows()
    {
        $expectedMessage = 'oihana\traits\UriTrait::buildUri failed with an invalid URI : http://[::1' ;

        $this->expectException( InvalidArgumentException::class );
        $this->expectExceptionMessageMatches(Char::SLASH . preg_quote( $expectedMessage, Char::SLASH ) . Char::SLASH );
        $this->obj->buildUri('http://[::1' );
    }

    public function testBuildUriEmptyFragmentAndQuery()
    {
        $uri = 'https://example.com/path?foo=1#section';
        $options = ['query' => [], 'fragment' => []];
        $expected = 'https://example.com/path?foo=1#section'; // fragment unchanged
        $result = $this->obj->buildUri($uri, $options);
        $this->assertSame($expected, $result);
    }
}