<?php

namespace tests\oihana\traits ;

use oihana\traits\QueryIDTrait;
use PHPUnit\Framework\TestCase;

class QueryIDTraitTest extends TestCase
{
    /**
     * Provide a concrete class to test the trait.
     */
    public function getQueryIDInstance(): object
    {
        return new class { use QueryIDTrait; };
    }

    public function testSetAndGetQueryIDWithString(): void
    {
        $obj = $this->getQueryIDInstance();
        $obj->initializeQueryID('custom_id');
        $this->assertSame('custom_id', $obj->getQueryID());
    }

    public function testInitializeQueryIdWithArrayContainingQueryId(): void
    {
        $obj = $this->getQueryIDInstance();
        $obj->initializeQueryID(['queryId' => 'array_id']);
        $this->assertSame('array_id', $obj->getQueryID());
    }

    public function testInitializeQueryIdWithArrayWithoutQueryId(): void
    {
        $obj = $this->getQueryIDInstance();
        $obj->initializeQueryID(['other' => 'value']);
        $id = $obj->getQueryID();
        $this->assertMatchesRegularExpression('/^query_\d+$/', $id);
    }

    public function testInitializeQueryIdWithNull(): void
    {
        $obj = $this->getQueryIDInstance();
        $obj->initializeQueryID(null);
        $id = $obj->getQueryID();
        $this->assertMatchesRegularExpression('/^query_\d+$/', $id);
    }

    public function testQueryIDIsAString(): void
    {
        $obj = $this->getQueryIDInstance();
        $obj->initializeQueryID(null);
        $this->assertIsString($obj->getQueryID());
    }
}