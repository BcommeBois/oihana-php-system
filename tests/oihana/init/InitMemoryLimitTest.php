<?php

namespace tests\oihana\init;

use oihana\reflect\exceptions\ConstantException;

use PHPUnit\Framework\TestCase;

use function oihana\init\initMemoryLimit;

/**
 * Note: the tests reuse the process' current memory_limit value so the
 * effective limit never changes — lowering it below the current usage
 * would kill the PHPUnit run.
 */
class InitMemoryLimitTest extends TestCase
{
    /**
     * @throws ConstantException
     */
    public function testInitMemoryLimitWithExplicitValue()
    {
        $current = (string) ini_get( 'memory_limit' ) ;
        $this->assertTrue( initMemoryLimit( $current ) ) ;
        $this->assertSame( $current , ini_get( 'memory_limit' ) ) ;
    }

    /**
     * @throws ConstantException
     */
    public function testInitMemoryLimitWithNullUsesDefault()
    {
        $current = (string) ini_get( 'memory_limit' ) ;
        $this->assertTrue( initMemoryLimit( null , $current ) ) ;
        $this->assertSame( $current , ini_get( 'memory_limit' ) ) ;
    }
}
