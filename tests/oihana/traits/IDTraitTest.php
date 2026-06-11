<?php

namespace tests\oihana\traits ;

use DI\Container;

use oihana\traits\IDTrait;

use PHPUnit\Framework\TestCase;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MockID
{
    use IDTrait;
}

class IDTraitTest extends TestCase
{
    private MockID $object;

    protected function setUp(): void
    {
        $this->object = new MockID();
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testDefaultIDIsNull()
    {
        $this->assertNull( $this->object->id ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeIDFromArray()
    {
        $result = $this->object->initializeID([ MockID::ID => 42 ]) ;
        $this->assertSame( 42 , $this->object->id ) ;
        $this->assertSame( $this->object , $result ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeIDKeepsCurrentValueWhenKeyIsAbsent()
    {
        $this->object->id = 'previous' ;
        $this->object->initializeID() ;
        $this->assertSame( 'previous' , $this->object->id ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeIDResolvesStringIDFromContainer()
    {
        $container = new Container() ;
        $container->set( 'service.id' , 12345 ) ;

        $this->object->initializeID([ MockID::ID => 'service.id' ] , $container ) ;

        $this->assertSame( 12345 , $this->object->id ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeIDKeepsStringIDWhenNotInContainer()
    {
        $container = new Container() ;
        $this->object->initializeID([ MockID::ID => 'plain-id' ] , $container ) ;
        $this->assertSame( 'plain-id' , $this->object->id ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeIDWithNullKeepsCurrentValue()
    {
        $this->object->id = 7 ;
        $this->object->initializeID([ MockID::ID => null ]) ;
        $this->assertSame( 7 , $this->object->id ) ;
    }
}
