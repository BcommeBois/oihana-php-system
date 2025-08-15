<?php

namespace tests\oihana\traits ;

use oihana\enums\Char;
use oihana\traits\UnsupportedTrait;
use PHPUnit\Framework\TestCase;
use oihana\exceptions\UnsupportedOperationException;

class UnsupportedTraitTest extends TestCase
{
    use UnsupportedTrait;

    protected function getObjectWithTrait() : object
    {
        return new class
        {
            use UnsupportedTrait;

            public function callUnsupported(string $method = Char::EMPTY): void
            {
                $this->unsupported($method);
            }
        };
    }

    public function testUnsupportedThrowsExceptionWithDefaultMethod()
    {
        $obj = $this->getObjectWithTrait();

        $expectedMessage = 'oihana\traits\UnsupportedTrait::unsupported' ;

        $this->expectException( UnsupportedOperationException::class );
        $this->expectExceptionMessageMatches(Char::SLASH . preg_quote( $expectedMessage, Char::SLASH ) . Char::SLASH );

        $obj->callUnsupported();
    }

    public function testUnsupportedThrowsExceptionWithMethodName()
    {
        $obj = $this->getObjectWithTrait();

        $expectedMessage = 'oihana\traits\UnsupportedTrait::unsupported::updateItem' ;

        $this->expectException( UnsupportedOperationException::class );
        $this->expectExceptionMessageMatches(Char::SLASH . preg_quote( $expectedMessage, Char::SLASH ) . Char::SLASH );

        $obj->callUnsupported( 'updateItem' );
    }
}