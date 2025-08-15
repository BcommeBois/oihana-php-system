<?php

namespace tests\oihana\traits ;

use oihana\enums\Char;
use oihana\traits\ToStringTrait;
use PHPUnit\Framework\TestCase;

class TestToStringDummyClass
{
    use ToStringTrait;
}

class ToStringTraitTest extends TestCase
{
    use ToStringTrait;

    public function testToStringReturnsClassNameInBrackets()
    {
        $obj = new TestToStringDummyClass();

        $expected = Char::LEFT_BRACKET . 'TestToStringDummyClass' . Char::RIGHT_BRACKET;
        $this->assertSame( $expected , (string) $obj ) ;
    }

    public function testToStringCachesClassName()
    {
        $obj = new TestToStringDummyClass();

        $firstCall  = (string) $obj;
        $secondCall = (string) $obj;

        $this->assertSame($firstCall, $secondCall);
    }
}