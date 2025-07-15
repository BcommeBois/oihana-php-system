<?php

namespace oihana\enums ;

use oihana\reflections\exceptions\ConstantException;
use PHPUnit\Framework\TestCase;

class CharTest extends TestCase
{
    public function testEnumsReturnsAllConstantsSorted(): void
    {
        $enums = Char::enums();

        $this->assertIsArray($enums);

        $this->assertContains('&', $enums);
        $this->assertContains('©', $enums);
        $this->assertContains('₀', $enums);  // subscript zero
        $this->assertContains('¹', $enums);  // superscript one

        $sorted = $enums;
        sort($sorted, SORT_STRING);
        $this->assertSame($sorted, $enums);
    }

    public function testIncludesReturnsTrueForKnownValue(): void
    {
        $this->assertTrue(Char::includes(Char::AMPERSAND));
        $this->assertTrue(Char::includes(Char::SUPERSCRIPT_ONE));
        $this->assertFalse(Char::includes('🌟'));
    }

    public function testGetConstantReturnsConstantName(): void
    {
        $this->assertSame('AMPERSAND', Char::getConstant('&'));
        $this->assertSame('SUBSCRIPT_ZERO', Char::getConstant('₀'));
        $this->assertNull(Char::getConstant('🌟'));
    }

    public function testValidateThrowsExceptionOnInvalidValue(): void
    {
        $this->expectException(\oihana\reflections\exceptions\ConstantException::class);
        Char::validate('🌟');
    }

    /**
     * @throws ConstantException
     */
    public function testValidateDoesNotThrowForValidValue(): void
    {
        $this->expectNotToPerformAssertions();
        Char::validate(Char::DOT);
    }
}