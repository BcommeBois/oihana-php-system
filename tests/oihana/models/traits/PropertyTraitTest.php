<?php

namespace tests\oihana\models\traits ;

use oihana\models\traits\PropertyTrait;
use PHPUnit\Framework\TestCase;

class PropertyTraitTest extends TestCase
{
    use PropertyTrait;

    public function testInitializePropertyWithArray()
    {
        $this->assertNull($this->property, 'Property should initially be null');

        $this->initializeProperty(['property' => 'foo']);

        $this->assertSame('foo', $this->property, 'Property should be set from init array');
    }

    public function testInitializePropertyReturnsSelf()
    {
        $return = $this->initializeProperty(['property' => 'baz']);

        $this->assertSame($this, $return, 'initializeProperty should return $this for chaining');
    }

    public function testInitializePropertyWithNullValue()
    {
        $this->property = 'initial';

        $this->initializeProperty(['property' => null]);

        $this->assertNull($this->property, 'Property should be set to null if init array has null');
    }
}