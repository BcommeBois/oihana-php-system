<?php

namespace tests\oihana\validations\rules;

use oihana\validations\enums\Rules;
use oihana\validations\rules\ISO8601DateTimeOrDurationRule;
use PHPUnit\Framework\TestCase;

final class ISO8601DateTimeOrDurationRuleTest extends TestCase
{
    public function testNameConstantMatchesRulesConstant(): void
    {
        $this->assertSame( Rules::ISO8601_DATE_TIME_OR_DURATION , ISO8601DateTimeOrDurationRule::NAME );
        $this->assertSame( 'iso8601_date_time_or_duration' , ISO8601DateTimeOrDurationRule::NAME );
    }

    public function testNullAndEmptyStringPass(): void
    {
        $rule = new ISO8601DateTimeOrDurationRule();
        $this->assertTrue( $rule->check( null ) );
        $this->assertTrue( $rule->check( '' ) );
    }

    public function testValidDateTimeBranch(): void
    {
        $rule = new ISO8601DateTimeOrDurationRule();
        $this->assertTrue( $rule->check( '2027-01-01T00:00:00Z' ) );
        $this->assertTrue( $rule->check( '2026-05-14T08:15:30+02:00' ) );
        $this->assertTrue( $rule->check( '2026-05-14T08:15:30.123Z' ) );
    }

    public function testValidDurationBranch(): void
    {
        $rule = new ISO8601DateTimeOrDurationRule();
        $this->assertTrue( $rule->check( 'P30D' ) );
        $this->assertTrue( $rule->check( 'P1Y' ) );
        $this->assertTrue( $rule->check( 'PT1H' ) );
        $this->assertTrue( $rule->check( 'P1Y2M3D' ) );
    }

    public function testCalendarDateOnlyIsRejected(): void
    {
        $rule = new ISO8601DateTimeOrDurationRule();
        $this->assertFalse( $rule->check( '2026-05-14' ) );
    }

    public function testInvalidValuesAreRejected(): void
    {
        $rule = new ISO8601DateTimeOrDurationRule();
        $this->assertFalse( $rule->check( 'not-a-date' ) );
        $this->assertFalse( $rule->check( 'P' ) );
        $this->assertFalse( $rule->check( '1Y2M' ) );
        $this->assertFalse( $rule->check( '2026-02-30T00:00:00Z' ) );
    }

    public function testLooseModeAcceptsSpaceSeparator(): void
    {
        $rule = new ISO8601DateTimeOrDurationRule( strict: false );
        $this->assertTrue( $rule->check( '2026-05-14 08:15:30' ) );
        $this->assertTrue( $rule->check( 'P30D' ) );
    }

    public function testNonStringValuesFail(): void
    {
        $rule = new ISO8601DateTimeOrDurationRule();
        $this->assertFalse( $rule->check( 12345 ) );
        $this->assertFalse( $rule->check( true ) );
        $this->assertFalse( $rule->check( [ 'P30D' ] ) );
    }

    public function testStringableObjectIsAccepted(): void
    {
        $dateTimeStringable = new class
        {
            public function __toString() :string
            {
                return '2027-01-01T00:00:00Z' ;
            }
        };

        $durationStringable = new class
        {
            public function __toString() :string
            {
                return 'P30D' ;
            }
        };

        $rule = new ISO8601DateTimeOrDurationRule();
        $this->assertTrue( $rule->check( $dateTimeStringable ) );
        $this->assertTrue( $rule->check( $durationStringable ) );
    }

    public function testCustomMessageIsApplied(): void
    {
        $rule = new ISO8601DateTimeOrDurationRule( message: 'Custom failure message.' );
        $reflection = new \ReflectionProperty( $rule , 'message' );
        $this->assertSame( 'Custom failure message.' , $reflection->getValue( $rule ) );
    }
}
