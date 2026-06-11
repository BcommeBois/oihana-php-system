<?php

namespace tests\oihana\logging;

use DI\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\NullLogger;

use oihana\logging\enums\LoggerParam;
use oihana\logging\LoggerManager;
use oihana\logging\LoggerManagerTrait;

use PHPUnit\Framework\TestCase;

class MockManagerHost
{
    use LoggerManagerTrait;
}

final class LoggerManagerTraitTest extends TestCase
{
    private MockManagerHost $host;

    protected function setUp(): void
    {
        $this->host = new MockManagerHost() ;
    }

    private function aManager(): LoggerManager
    {
        return new class( [ LoggerParam::DIRECTORY => sys_get_temp_dir() ] ) extends LoggerManager
        {
            public function createLogger(): \Psr\Log\LoggerInterface
            {
                return new NullLogger() ;
            }
        } ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeWithInstance(): void
    {
        $manager = $this->aManager() ;
        $result  = $this->host->initializeLoggerManager( $manager ) ;

        $this->assertSame( $manager , $this->host->manager ) ;
        $this->assertSame( $this->host , $result ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeWithNull(): void
    {
        $this->host->initializeLoggerManager( null ) ;
        $this->assertNull( $this->host->manager ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeResolvesStringFromContainer(): void
    {
        $manager   = $this->aManager() ;
        $container = new Container() ;
        $container->set( 'logger.manager' , $manager ) ;

        $this->host->initializeLoggerManager( 'logger.manager' , $container ) ;

        $this->assertSame( $manager , $this->host->manager ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeWithStringNotInContainerYieldsNull(): void
    {
        $this->host->initializeLoggerManager( 'missing' , new Container() ) ;
        $this->assertNull( $this->host->manager ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeWithContainerEntryThatIsNotAManagerYieldsNull(): void
    {
        $container = new Container() ;
        $container->set( 'not.a.manager' , new \stdClass() ) ;

        $this->host->initializeLoggerManager( 'not.a.manager' , $container ) ;

        $this->assertNull( $this->host->manager ) ;
    }
}
