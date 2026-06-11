<?php

namespace tests\oihana\traits ;

use oihana\traits\LockableTrait;

use PHPUnit\Framework\TestCase;

class MockLockable
{
    use LockableTrait;

    public function __construct( array $init = [] )
    {
        $this->lockable = $this->initLockable( $init ) ;
    }
}

class LockableTraitTest extends TestCase
{
    public function testDefaultLockableIsFalse()
    {
        $object = new MockLockable() ;
        $this->assertFalse( $object->lockable ) ;
    }

    public function testInitLockableFromArray()
    {
        $object = new MockLockable([ MockLockable::LOCKABLE => true ]) ;
        $this->assertTrue( $object->lockable ) ;
    }

    public function testInitLockableWithNonBooleanReturnsFalse()
    {
        $object = new MockLockable([ MockLockable::LOCKABLE => 'yes' ]) ;
        $this->assertFalse( $object->lockable ) ;
    }
}
