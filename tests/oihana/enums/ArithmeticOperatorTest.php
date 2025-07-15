<?php

namespace oihana\enums ;

use oihana\reflections\exceptions\ConstantException;
use PHPUnit\Framework\TestCase;

class ArithmeticOperatorTest extends TestCase
{
    public function testEnumsReturnsAllOperatorsSorted(): void
    {
        $enums = ArithmeticOperator::enums();

        // Vérifie que c'est un tableau
        $this->assertIsArray($enums);

        // Vérifie que toutes les constantes sont présentes
        $expectedValues = [
            ArithmeticOperator::ADDITION,
            ArithmeticOperator::DIVISION,
            ArithmeticOperator::EXPONENT,
            ArithmeticOperator::MODULO,
            ArithmeticOperator::MULTIPLICATION,
            ArithmeticOperator::SUBSTRACTION,
        ];

        foreach ($expectedValues as $value) {
            $this->assertContains($value, $enums);
        }

        // Vérifie que le tableau est trié (tri string par défaut)
        $sorted = $enums;
        sort($sorted, SORT_STRING);
        $this->assertSame($sorted, $enums);
    }

    public function testIncludesReturnsTrueForKnownOperator(): void
    {
        $this->assertTrue(ArithmeticOperator::includes('+'));
        $this->assertTrue(ArithmeticOperator::includes('**'));
        $this->assertFalse(ArithmeticOperator::includes('^'));
    }

    public function testGetConstantReturnsNameForValue(): void
    {
        $this->assertSame('ADDITION', ArithmeticOperator::getConstant('+'));
        $this->assertSame('EXPONENT', ArithmeticOperator::getConstant('**'));
        $this->assertNull(ArithmeticOperator::getConstant('^'));
    }

    public function testValidateThrowsExceptionOnInvalidOperator(): void
    {
        $this->expectException(ConstantException::class);
        ArithmeticOperator::validate('^');
    }

    public function testValidatePassesOnValidOperator(): void
    {
        $this->expectNotToPerformAssertions();
        ArithmeticOperator::validate('*');
    }
}