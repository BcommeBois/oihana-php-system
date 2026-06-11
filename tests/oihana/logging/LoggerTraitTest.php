<?php

namespace tests\oihana\logging;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use oihana\logging\Logger;
use oihana\logging\LoggerTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

class MockLogger
{
    use LoggerTrait;

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function __construct( Container $container , array|LoggerInterface|null $init = null )
    {
        $this->initializeLogger( $init , $container );
    }
}

class LoggerTraitTest extends TestCase
{
    private Container       $container;
    private LoggerInterface $logger;
    private object          $testObj ;

    protected function setUp(): void
    {
        $tmpDir          = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'oihana-php-system' . DIRECTORY_SEPARATOR ;

        $this->container  = new Container() ;
        $this->logger     = new Logger( $tmpDir ) ;

        $this->container->set( 'my-logger' , $this->logger );

        $this->testObj = new class
        {
            use LoggerTrait;
        };
    }

    public function testInitLoggerWithUseDefaultFalse(): void
    {
        $testObj = new class($this->container)
        {
            use LoggerTrait;

            public function __construct(Container $container)
            {
                $this->initializeLogger('non-existing-logger', $container, false);
            }
        };

        $this->assertNull($testObj->getLogger());
    }


    public function testInitLoggableSetsFlag(): void
    {
        $this->testObj->initializeLoggable(['loggable' => true]);
        $this->assertTrue($this->testObj->loggable);

        $this->testObj->initializeLoggable(['loggable' => false]);
        $this->assertFalse($this->testObj->loggable);
    }

    public function testInitLoggerWithObject(): void
    {
        $this->testObj->initializeLogger($this->logger);
        $this->assertSame($this->logger, $this->testObj->getLogger() );
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testInitLoggerWithArrayAndContainer(): void
    {
        $testObj = new MockLogger( $this->container ,  ['logger' => 'my-logger' ] ) ;
        $this->assertSame( $this->logger , $testObj->getLogger());
    }

    public function testAllPsrLevelMethodsDelegateToTheLogger(): void
    {
        $spy = new class implements LoggerInterface
        {
            use \Psr\Log\LoggerTrait ;
            public array $calls = [] ;
            public function log( $level , string|\Stringable $message , array $context = [] ) : void
            {
                $this->calls[] = [ $level , (string) $message ] ;
            }
        } ;

        $this->testObj->setLogger( $spy ) ;

        $this->testObj->emergency( 'a' ) ;
        $this->testObj->alert    ( 'b' ) ;
        $this->testObj->critical ( 'c' ) ;
        $this->testObj->error    ( 'd' ) ;
        $this->testObj->warning  ( 'e' ) ;
        $this->testObj->notice   ( 'f' ) ;
        $this->testObj->info     ( 'g' ) ;
        $this->testObj->debug    ( 'h' ) ;
        $this->testObj->log( 'custom' , 'i' ) ;

        $this->assertCount( 9 , $spy->calls ) ;
        $this->assertSame( 'custom' , $spy->calls[8][0] ) ;
        $this->assertSame( 'i' , $spy->calls[8][1] ) ;
    }

    public function testLevelMethodsAreNoOpWithoutLogger(): void
    {
        // No logger set: the nullsafe calls must not raise.
        $this->testObj->info( 'no logger' ) ;
        $this->testObj->error( 'no logger' ) ;
        $this->addToAssertionCount( 1 ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testInitializeLoggableResolvesFromContainer(): void
    {
        $container = new Container() ;
        $container->set( 'loggable' , true ) ;

        $this->testObj->initializeLoggable( null , $container ) ;
        $this->assertTrue( $this->testObj->loggable ) ;
    }

    public function testInitializeLoggableWithBoolean(): void
    {
        $this->testObj->initializeLoggable( true ) ;
        $this->assertTrue( $this->testObj->loggable ) ;
    }

    public function testInitializeLoggableFallsBackToDefault(): void
    {
        $this->testObj->initializeLoggable( [] , null , true ) ;
        $this->assertTrue( $this->testObj->loggable ) ;
    }
}
