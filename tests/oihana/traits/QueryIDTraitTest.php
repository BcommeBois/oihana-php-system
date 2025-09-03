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
        $obj->setQueryID('custom_id');
        $this->assertSame('custom_id', $obj->getQueryID());
    }

    public function testSetQueryIDWithArrayContainingQueryId(): void
    {
        $obj = $this->getQueryIDInstance();
        $obj->setQueryID(['queryId' => 'array_id']);
        $this->assertSame('array_id', $obj->getQueryID());
    }

    public function testSetQueryIDWithArrayWithoutQueryId(): void
    {
        $obj = $this->getQueryIDInstance();
        $obj->setQueryID(['other' => 'value']);
        $id = $obj->getQueryID();
        $this->assertMatchesRegularExpression('/^query_\d+$/', $id);
    }

    public function testSetQueryIDWithNull(): void
    {
        $obj = $this->getQueryIDInstance();
        $obj->setQueryID(null);
        $id = $obj->getQueryID();
        $this->assertMatchesRegularExpression('/^query_\d+$/', $id);
    }

    public function testQueryIDIsAString(): void
    {
        $obj = $this->getQueryIDInstance();
        $obj->setQueryID(null);
        $this->assertIsString($obj->getQueryID());
    }
}