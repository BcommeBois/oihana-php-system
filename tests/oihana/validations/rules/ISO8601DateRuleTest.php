<?php

namespace tests\oihana\validations\rules;

use oihana\validations\rules\ISO8601DateRule;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ISO8601DateRuleTest extends TestCase
{
    public static function validDates(): iterable
    {
        yield 'date'              => [ '2024-01-15' ] ;
        yield 'date time utc'     => [ '2024-01-15T10:30:00Z' ] ;
        yield 'date time offset'  => [ '2024-01-15T10:30:00+02:00' ] ;
        yield 'year only'         => [ '2024' ] ;
        yield 'week date'         => [ '2024-W05-1' ] ;
    }

    public static function invalidDates(): iterable
    {
        yield 'null'          => [ null ] ;
        yield 'empty string'  => [ '' ] ;
        yield 'not a date'    => [ 'not-a-date' ] ;
        yield 'invalid month' => [ '2024-13-01' ] ;
        yield 'integer'       => [ 123 ] ;
        yield 'array'         => [ [ '2024-01-15' ] ] ;
    }

    #[DataProvider('validDates')]
    public function testValidISO8601Date( string $value ): void
    {
        $rule = new ISO8601DateRule() ;
        $this->assertTrue( $rule->check( $value ) , sprintf( 'Failed asserting that %s is a valid ISO8601 date.' , $value ) ) ;
    }

    #[DataProvider('invalidDates')]
    public function testInvalidISO8601Date( mixed $value ): void
    {
        $rule = new ISO8601DateRule() ;
        $this->assertFalse( $rule->check( $value ) ) ;
    }

    public function testStringableValueIsAccepted(): void
    {
        $value = new class
        {
            public function __toString(): string
            {
                return '2024-01-15' ;
            }
        } ;

        $rule = new ISO8601DateRule() ;
        $this->assertTrue( $rule->check( $value ) ) ;
    }

    public function testCustomPatternAndMessage(): void
    {
        $rule = new ISO8601DateRule( '/^\d{4}$/' , 'Custom message' ) ;

        $this->assertTrue ( $rule->check( '2024' ) ) ;
        $this->assertFalse( $rule->check( '2024-01-15' ) ) ;
    }
}
