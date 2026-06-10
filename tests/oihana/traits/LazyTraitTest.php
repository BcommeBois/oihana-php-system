<?php

namespace tests\oihana\traits;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use oihana\traits\LazyTrait;

use PHPUnit\Framework\TestCase;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class LazyTraitTest extends TestCase
{
    use LazyTrait;

    protected function setUp(): void
    {
        $this->lazy = true ;
        unset( $this->container ) ;
    }

    public function testLazyIsTrueByDefault()
    {
        $this->assertTrue( $this->lazy ) ;
    }

    public function testInitializeLazyWithBoolean()
    {
        $this->initializeLazy([ self::LAZY => false ]) ;
        $this->assertFalse( $this->lazy ) ;

        $this->initializeLazy([ self::LAZY => true ]) ;
        $this->assertTrue( $this->lazy ) ;
    }

    public function testInitializeLazyWithoutKeyKeepsCurrentValue()
    {
        $this->lazy = false ;
        $this->initializeLazy() ;
        $this->assertFalse( $this->lazy ) ;

        $this->lazy = true ;
        $this->initializeLazy() ;
        $this->assertTrue( $this->lazy ) ;
    }

    public function testInitializeLazyCastsNonBooleanValues()
    {
        $this->initializeLazy([ self::LAZY => 0 ]) ;
        $this->assertFalse( $this->lazy ) ;

        $this->initializeLazy([ self::LAZY => 'yes' ]) ;
        $this->assertTrue( $this->lazy ) ;
    }

    public function testInitializeLazyAssignsContainer()
    {
        $container = new Container() ;
        $this->initializeLazy( container: $container ) ;
        $this->assertSame( $container , $this->container ) ;
    }

    public function testInitializeLazyWithoutContainerKeepsCurrentReference()
    {
        $container = new Container() ;
        $this->container = $container ;
        $this->initializeLazy() ;
        $this->assertSame( $container , $this->container ) ;
    }

    public function testInitializeLazyIsFluent()
    {
        $this->assertSame( $this , $this->initializeLazy() ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testIsLazyReturnsPropertyWithoutContainer()
    {
        $this->lazy = true ;
        $this->assertTrue( $this->isLazy() ) ;

        $this->lazy = false ;
        $this->assertFalse( $this->isLazy() ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testIsLazyContainerEntryTakesPrecedence()
    {
        $container = new Container() ;
        $container->set( self::LAZY , false ) ;

        $this->initializeLazy([ self::LAZY => true ] , $container ) ;

        $this->assertTrue ( $this->lazy ) ;
        $this->assertFalse( $this->isLazy() ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testIsLazyFallsBackToPropertyWhenContainerHasNoEntry()
    {
        $this->lazy = false ;
        $this->container = new Container() ;
        $this->assertFalse( $this->isLazy() ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testIsLazyCastsContainerValueToBoolean()
    {
        $container = new Container() ;
        $container->set( self::LAZY , 1 ) ;
        $this->container = $container ;
        $this->assertTrue( $this->isLazy() ) ;

        $container->set( self::LAZY , 0 ) ;
        $this->assertFalse( $this->isLazy() ) ;
    }
}
