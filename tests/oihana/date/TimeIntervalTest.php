<?php

namespace tests\oihana\date;

use oihana\date\TimeInterval;
use PHPUnit\Framework\TestCase;

class TimeIntervalTest extends TestCase
{
    public function testFormattedBasic()
    {
        $ti = new TimeInterval('7:31');
        $this->assertSame('7:31', $ti->formatted());
    }

    public function testHumanizeBasic()
    {
        $ti = new TimeInterval('7:31');
        $this->assertSame('7m 31s', $ti->humanize());
    }

    public function testToSeconds()
    {
        $ti = new TimeInterval('1h 2m 5s');
        $this->assertEquals(3725, $ti->toSeconds());
    }

    public function testToMinutesWithPrecision()
    {
        $ti = new TimeInterval('1h 2m 5s');
        $this->assertEquals(62.08, $ti->toMinutes(null, 2));
    }

    public function testToMinutesRounded()
    {
        $ti = new TimeInterval('1h 2m 5s');
        $this->assertEquals(62, $ti->toMinutes(null, 0));
    }

    public function testDaysAndCustomHoursPerDay()
    {
        $ti = new TimeInterval('1.5d 1.5h 2m 5s', 6 ) ;
        $this->assertSame('1d 4h 32m 5s', $ti->humanize());
        $this->assertSame('10:32:05', $ti->formatted());
        $this->assertEquals(632, $ti->toMinutes(null, 0));
    }

    public function testPureSecondsInput()
    {
        $ti = new TimeInterval(4293);
        $this->assertSame('1h 11m 33s', $ti->humanize());
        $this->assertSame('1:11:33', $ti->formatted());
        $this->assertEquals(4293, $ti->toSeconds());
        $this->assertEquals(71.55, $ti->toMinutes(null, 2));
    }

    public function testColonFormatTwoParts()
    {
        $ti = new TimeInterval('10:20');
        $this->assertSame('10m 20s', $ti->humanize());
        $this->assertSame('10:20', $ti->formatted());
    }

    public function testColonFormatThreeParts()
    {
        $ti = new TimeInterval('1:02:05');
        $this->assertSame('1h 2m 5s', $ti->humanize());
        $this->assertSame('1:02:05', $ti->formatted());
    }

    public function testZeroFill()
    {
        $ti = new TimeInterval('28');
        $this->assertSame('0:00:28', $ti->formatted(null, true));
    }

    public function testEmptyDuration()
    {
        $ti = new TimeInterval(null);
        $this->assertSame('0s', $ti->humanize());
        $this->assertSame('0', $ti->formatted());
        $this->assertEquals(0, $ti->toSeconds());
    }

    public function testInvalidDuration()
    {
        $ti = new TimeInterval('invalid string');
        $this->assertSame('0', $ti->formatted());
    }
}