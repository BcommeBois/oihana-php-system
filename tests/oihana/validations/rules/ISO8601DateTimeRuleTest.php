<?php

namespace tests\oihana\validations\rules;

use oihana\validations\enums\Rules;
use oihana\validations\rules\ISO8601DateTimeRule;
use PHPUnit\Framework\TestCase;

final class ISO8601DateTimeRuleTest extends TestCase
{
    public function testNameConstantMatchesRulesConstant(): void
    {
        $this->assertSame( Rules::ISO8601_DATE_TIME , ISO8601DateTimeRule::NAME );
        $this->assertSame( 'iso8601_date_time' , ISO8601DateTimeRule::NAME );
    }

    public function testNullAndEmptyStringPass(): void
    {
        $rule = new ISO8601DateTimeRule();
        $this->assertTrue( $rule->check( null ) );
        $this->assertTrue( $rule->check( '' ) );
    }

    public function testValidExpressionsInStrictMode(): void
    {
        $rule = new ISO8601DateTimeRule();
        $this->assertTrue( $rule->check( '2026-05-14T08:15:30Z' ) );
        $this->assertTrue( $rule->check( '2026-05-14T08:15:30+02:00' ) );
        $this->assertTrue( $rule->check( '2026-05-14T08:15:30.123Z' ) );
        $this->assertTrue( $rule->check( '2026-05-14T08:15:30-05:30' ) );
    }

    public function testInvalidExpressionsInStrictMode(): void
    {
        $rule = new ISO8601DateTimeRule();
        $this->assertFalse( $rule->check( '2026-05-14' ) );           // date only
        $this->assertFalse( $rule->check( '2026-05-14 08:15:30' ) );  // space separator
        $this->assertFalse( $rule->check( '2026-02-30T00:00:00Z' ) ); // invalid calendar date
        $this->assertFalse( $rule->check( 'not-a-date' ) );
    }

    public function testSpaceSeparatorAllowedInLooseMode(): void
    {
        $rule = new ISO8601DateTimeRule( strict: false );
        $this->assertTrue( $rule->check( '2026-05-14 08:15:30' ) );
        $this->assertTrue( $rule->check( '2026-05-14T08:15:30Z' ) );
    }

    public function testNonStringValuesFail(): void
    {
        $rule = new ISO8601DateTimeRule();
        $this->assertFalse( $rule->check( 12345 ) );
        $this->assertFalse( $rule->check( true ) );
        $this->assertFalse( $rule->check( [ '2026-05-14T08:15:30Z' ] ) );
    }

    public function testStringableObjectIsAccepted(): void
    {
        $stringable = new class
        {
            public function __toString() :string
            {
                return '2026-05-14T08:15:30Z' ;
            }
        };

        $rule = new ISO8601DateTimeRule();
        $this->assertTrue( $rule->check( $stringable ) );
    }

    public function testCustomMessageIsApplied(): void
    {
        $rule = new ISO8601DateTimeRule( message: 'Custom failure message.' );
        $reflection = new \ReflectionProperty( $rule , 'message' );
        $this->assertSame( 'Custom failure message.' , $reflection->getValue( $rule ) );
    }
}
