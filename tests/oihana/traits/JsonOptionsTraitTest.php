<?php

namespace tests\oihana\traits ;

use oihana\traits\JsonOptionsTrait;

use PHPUnit\Framework\TestCase;

class MockJsonOptions
{
    use JsonOptionsTrait;

    public function __construct( array $init = [] )
    {
        $this->initializeJsonOptions( $init ) ;
    }
}

class JsonOptionsTraitTest extends TestCase
{
    public function testDefaultJsonOptions()
    {
        $object = new MockJsonOptions() ;
        $this->assertSame( JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT , $object->jsonOptions ) ;
    }

    public function testInitializeJsonOptionsFromArray()
    {
        $object = new MockJsonOptions([ MockJsonOptions::JSON_OPTIONS => JSON_HEX_TAG ]) ;
        $this->assertSame( JSON_HEX_TAG , $object->jsonOptions ) ;
    }

    public function testInitializeJsonOptionsKeepsCurrentValueWhenKeyIsAbsent()
    {
        $object = new MockJsonOptions([ 'other' => 123 ]) ;
        $this->assertSame( JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT , $object->jsonOptions ) ;
    }
}
