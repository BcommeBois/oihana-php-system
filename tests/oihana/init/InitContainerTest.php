<?php

namespace tests\oihana\init;

use DI\Container;

use Exception;

use PHPUnit\Framework\TestCase;

use function oihana\init\initContainer;

class InitContainerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testInitContainerBuildsContainerFromArray()
    {
        $container = initContainer([ 'foo' => 'bar' ]) ;

        $this->assertInstanceOf( Container::class , $container ) ;
        $this->assertSame( 'bar' , $container->get( 'foo' ) ) ;
    }

    /**
     * @throws Exception
     */
    public function testInitContainerLaterDefinitionsOverrideEarlierOnes()
    {
        $container = initContainer([ 'key' => 'first' , 'kept' => 1 ] , [ 'key' => 'second' ]) ;

        $this->assertSame( 'second' , $container->get( 'key' ) ) ;
        $this->assertSame( 1 , $container->get( 'kept' ) ) ;
    }
}
