<?php

namespace tests\oihana\traits ;

use oihana\traits\KeyValueTrait;
use PHPUnit\Framework\TestCase;
use stdClass;

class KeyValueTraitTest extends TestCase
{
    use KeyValueTrait;

    public function testGetKeyValueFromArray():void
    {
        $document = ['foo' => 'bar', 'baz' => 123];
        $this->assertSame('bar', $this->getKeyValue($document, 'foo'));
        $this->assertSame(123, $this->getKeyValue($document, 'baz'));
        $this->assertNull($this->getKeyValue($document, 'missing'));
    }

    public function testGetKeyValueFromObject():void
    {
        $document = new stdClass();
        $document->foo = 'bar';
        $document->baz = 123;

        $this->assertSame('bar', $this->getKeyValue($document, 'foo'));
        $this->assertSame(123, $this->getKeyValue($document, 'baz'));
        $this->assertNull($this->getKeyValue($document, 'missing'));
    }

    public function testGetKeyValueWithExplicitIsArrayFlag():void
    {
        $document = ['foo' => 'bar'] ;
        $this->assertEquals( 'bar' , $this->getKeyValue( $document , 'foo' , true ) );

        $object = new stdClass();
        $object->foo = 'bar';
        $this->assertEquals( 'bar' ,  $this->getKeyValue($object, 'foo', false));
    }

    public function testSetKeyValueOnArray():void
    {
        $document = ['foo' => 'bar'];
        $updated = $this->setKeyValue($document, 'baz', 123);
        $this->assertIsArray($updated);
        $this->assertSame(123, $updated['baz']);
        $this->assertSame('bar', $updated['foo']);
    }

    public function testSetKeyValueOnObject():void
    {
        $document = new stdClass();
        $document->foo = 'bar';

        $updated = $this->setKeyValue($document, 'baz', 123);
        $this->assertInstanceOf(stdClass::class, $updated);
        $this->assertSame(123, $updated->baz);
        $this->assertSame('bar', $updated->foo);
    }

    public function testSetKeyValueWithExplicitIsArrayFlag():void
    {
        $document = ['foo' => 'bar'];

        $result = $this->setKeyValue( $document, 'foo', 123, true );
        $this->assertIsArray( $result );
        $this->assertEquals( 123 , $result[ 'foo' ] );

        $object = new stdClass();
        $object->foo = 'bar';
        $result = $this->setKeyValue($object, 'baz', 123, false);
        $this->assertIsObject( $result );
        $this->assertEquals( 123 , $result->baz );
    }
}