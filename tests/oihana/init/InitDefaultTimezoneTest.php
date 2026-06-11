<?php

namespace tests\oihana\init;

use PHPUnit\Framework\TestCase;

use function oihana\init\initDefaultTimezone;

class InitDefaultTimezoneTest extends TestCase
{
    private string $original;

    protected function setUp(): void
    {
        $this->original = date_default_timezone_get() ;
    }

    protected function tearDown(): void
    {
        date_default_timezone_set( $this->original ) ;
    }

    public function testInitDefaultTimezoneWithExplicitIdentifier()
    {
        initDefaultTimezone( 'UTC' ) ;
        $this->assertSame( 'UTC' , date_default_timezone_get() ) ;
    }

    public function testInitDefaultTimezoneWithNullUsesDefault()
    {
        initDefaultTimezone( null , 'Europe/Lisbon' ) ;
        $this->assertSame( 'Europe/Lisbon' , date_default_timezone_get() ) ;
    }
}
