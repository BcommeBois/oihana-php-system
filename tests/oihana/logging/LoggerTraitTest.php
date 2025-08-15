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
    private LoggerInterface $loggerMock;
    private object          $testObj ;

    protected function setUp(): void
    {
        $tmpDir          = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'oihana-php-system' . DIRECTORY_SEPARATOR ;

        $this->container  = new Container() ;
        $this->logger     = new Logger( $tmpDir ) ;
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->container->set( 'my-logger' , $this->logger );

        $this->testObj = new class
        {
            use LoggerTrait;
        };
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
}
