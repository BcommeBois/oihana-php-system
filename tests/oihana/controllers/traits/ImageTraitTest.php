<?php

namespace tests\oihana\controllers\traits;

use oihana\controllers\traits\ImageTrait;

use PHPUnit\Framework\TestCase;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class ImageTraitTest extends TestCase
{
    private object $mock;

    protected function setUp(): void
    {
        $this->mock = new class
        {
            use ImageTrait;
        };
    }

    /**
     * Builds a passive Response stub whose builder methods return self and
     * whose body accepts writes (mirrors StatusTraitTest).
     */
    private function response(): ResponseInterface
    {
        $stream = $this->createStub( StreamInterface::class );
        $stream->method('write')->willReturnCallback( fn( $data ) => strlen( (string) $data ) );

        $response = $this->createStub( ResponseInterface::class );
        $response->method('getBody')->willReturn( $stream );
        $response->method('withStatus')->willReturnSelf();
        $response->method('withHeader')->willReturnSelf();
        $response->method('withBody')->willReturnSelf();

        return $response;
    }

    /**
     * Non-regression: a failing Imagick load must go through the catch and
     * return a proper 500 response instead of raising a TypeError because
     * fail() was called with the wrong argument slots.
     *
     * @see CHANGELOG "Fixed > Controllers"
     */
    public function testImagickResponseFailureReturnsResponseInsteadOfTypeError(): void
    {
        $response = $this->response();

        $result = $this->mock->imagickResponse( $response , '/this/path/does/not/exist.jpg' );

        $this->assertSame( $response , $result ) ;
    }
}
