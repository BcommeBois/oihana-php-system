<?php

namespace tests\oihana\validations\rules;

use oihana\validations\enums\Rules;
use oihana\validations\rules\ISO8601DurationRule;
use PHPUnit\Framework\TestCase;

final class ISO8601DurationRuleTest extends TestCase
{
    public function testNameConstantMatchesRulesConstant(): void
    {
        $this->assertSame( Rules::ISO8601_DURATION , ISO8601DurationRule::NAME );
        $this->assertSame( 'iso8601_duration' , ISO8601DurationRule::NAME );
    }

    public function testNullAndEmptyStringPass(): void
    {
        $rule = new ISO8601DurationRule();
        $this->assertTrue( $rule->check( null ) );
        $this->assertTrue( $rule->check( '' ) );
    }

    public function testValidExpressionsInStrictMode(): void
    {
        $rule = new ISO8601DurationRule();
        $this->assertTrue( $rule->check( 'P1Y2M3D' ) );
        $this->assertTrue( $rule->check( 'PT4H30M' ) );
        $this->assertTrue( $rule->check( 'P1W' ) );
        $this->assertTrue( $rule->check( 'P30D' ) );
        $this->assertTrue( $rule->check( 'P0D' ) );
    }

    public function testInvalidExpressionsInStrictMode(): void
    {
        $rule = new ISO8601DurationRule();
        $this->assertFalse( $rule->check( 'P' ) );        // no components
        $this->assertFalse( $rule->check( 'PT' ) );       // T without time components
        $this->assertFalse( $rule->check( '1Y2M' ) );     // missing P
        $this->assertFalse( $rule->check( 'P1.5Y' ) );    // decimals rejected in strict
        $this->assertFalse( $rule->check( 'invalid' ) );
    }

    public function testLooseModeDelegatesToDateInterval(): void
    {
        $rule = new ISO8601DurationRule( strict: false );
        $this->assertTrue( $rule->check( 'P1Y' ) );
        $this->assertTrue( $rule->check( 'PT1H' ) );
        $this->assertFalse( $rule->check( 'invalid' ) );
    }

    public function testNonStringValuesFail(): void
    {
        $rule = new ISO8601DurationRule();
        $this->assertFalse( $rule->check( 12345 ) );
        $this->assertFalse( $rule->check( true ) );
        $this->assertFalse( $rule->check( [ 'P1Y' ] ) );
    }

    public function testStringableObjectIsAccepted(): void
    {
        $stringable = new class
        {
            public function __toString() :string
            {
                return 'P30D' ;
            }
        };

        $rule = new ISO8601DurationRule();
        $this->assertTrue( $rule->check( $stringable ) );
    }

    public function testCustomMessageIsApplied(): void
    {
        $rule = new ISO8601DurationRule( message: 'Custom failure message.' );
        $reflection = new \ReflectionProperty( $rule , 'message' );
        $this->assertSame( 'Custom failure message.' , $reflection->getValue( $rule ) );
    }
}
