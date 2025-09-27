<?php

namespace tests\oihana\traits ;

use oihana\traits\DebugTrait;
use PHPUnit\Framework\TestCase;

class DebugTraitTest extends TestCase
{
    use DebugTrait;

    public function setUp(): void
    {
        $this->debug = false;
        $this->mock = false;
    }

    public function testInitializeDebugWithBoolean()
    {
        $this->initializeDebug([ self::DEBUG => true]);
        $this->assertTrue($this->debug);

        $this->initializeDebug([self::DEBUG => false]);
        $this->assertFalse($this->debug);
    }

    public function testInitializeDebugWithNonBooleanUsesDefault()
    {
        $this->initializeDebug([self::DEBUG => 'foo'], true);
        $this->assertTrue($this->debug);

        $this->initializeDebug([self::DEBUG => ['array']], false);
        $this->assertFalse($this->debug);
    }

    public function testInitializeMockWithBoolean()
    {
        $this->initializeMock([self::MOCK => true]);
        $this->assertTrue($this->mock);

        $this->initializeMock([self::MOCK => false]);
        $this->assertFalse($this->mock);
    }

    public function testInitializeMockWithNonBooleanUsesDefault()
    {
        // non-boolean init, default = true
        $this->initializeMock([self::MOCK => 123], true);
        $this->assertTrue($this->mock); // OK, defaultValue = true

        // non-boolean init, default = false
        $this->initializeMock([self::MOCK => null], false);
        $this->assertFalse($this->mock); // OK, defaultValue = false
    }

    public function testIsDebug()
    {
        $this->debug = true;
        $this->assertTrue($this->isDebug());

        $this->debug = false;
        $this->assertFalse($this->isDebug());

        $this->assertTrue($this->isDebug([self::DEBUG => true]));
        $this->assertFalse($this->isDebug([self::DEBUG => false]));
    }

    public function testIsMockDependsOnDebug()
    {
        $this->debug = false;
        $this->mock = true;
        $this->assertFalse($this->isMock()); // debug off → mock off

        $this->debug = true;
        $this->mock = true;
        $this->assertTrue($this->isMock()); // debug on → mock on

        // test override via init array
        $this->debug = false;
        $this->mock = false;
        $this->assertTrue($this->isMock([self::DEBUG => true, self::MOCK => true]));
        $this->assertFalse($this->isMock([self::DEBUG => true, self::MOCK => false]));
    }

    public function testIsDebugAndIsMockWithNonBooleanInit()
    {
        $this->debug = false;
        $this->mock = false;

        $this->assertFalse($this->isDebug([self::DEBUG => 'yes']));
        $this->assertFalse($this->isMock([self::DEBUG => 'yes', self::MOCK => 'yes']));
    }
}