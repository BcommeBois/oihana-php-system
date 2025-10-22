<?php

namespace tests\oihana\traits ;

use oihana\traits\SortDefaultTrait;
use PHPUnit\Framework\TestCase;

class MockSortDefault
{
    use SortDefaultTrait;

    public function __construct(array $init = [], ?string $default = null)
    {
        $this->initializeSortDefault($init, $default);
    }
}

class SortDefaultTraitTest extends TestCase
{
    private function createTraitInstance(array $init = [], ?string $default = null) : MockSortDefault
    {
        return new MockSortDefault($init, $default) ;
    }

    public function testDefaultIsNull()
    {
        $obj = $this->createTraitInstance();
        $this->assertNull($obj->sortDefault, 'sortDefault should be null by default');
    }

    public function testInitializeWithArrayValue()
    {
        $obj = $this->createTraitInstance([MockSortDefault::SORT_DEFAULT => 'name ASC']);
        $this->assertSame('name ASC', $obj->sortDefault, 'sortDefault should be set from init array');
    }

    public function testInitializeWithDefaultValue()
    {
        $obj = $this->createTraitInstance([], 'age DESC');
        $this->assertSame('age DESC', $obj->sortDefault, 'sortDefault should fall back to default value');
    }

    public function testInitializeArrayHasPriorityOverDefault()
    {
        $obj = $this->createTraitInstance([MockSortDefault::SORT_DEFAULT => 'name ASC'], 'age DESC');
        $this->assertSame('name ASC', $obj->sortDefault, 'sortDefault from init array should override default value');
    }

    public function testInitializeRetainsExistingValue()
    {
        $obj = $this->createTraitInstance();
        $obj->sortDefault = 'email ASC';
        $obj->initializeSortDefault([], 'age DESC');
        $this->assertSame('email ASC', $obj->sortDefault, 'Existing sortDefault should not be overridden by default');
    }
}