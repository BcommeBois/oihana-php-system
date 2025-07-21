<?php

namespace oihana\logging;

use Stringable;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

use oihana\enums\Char;
use oihana\enums\Param;

trait LoggerTrait
{
    use LoggerAwareTrait ;

    /**
     * The logger parameter constant.
     */
    public const string LOGGER = 'logger' ;

    /**
     * The 'loggable' parameter constant.
     */
    public const string LOGGABLE = 'loggable' ;

    /**
     * The loggable flag.
     * @var bool
     */
    public bool $loggable = false ;

    /**
     * System is unusable.
     * @param string|Stringable $message
     * @param array $context
     * @return void
     */
    public function emergency( string|Stringable $message, array $context = [] ):void
    {
        $this->logger?->notice( $message , $context );
    }

    /**
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     * @param string|Stringable  $message
     * @param array $context
     * @return void
     */
    public function alert( string|Stringable $message, array $context = [] ):void
    {
        $this->logger?->alert( $message , $context );
    }

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     * @param string|Stringable $message
     * @param array $context
     * @return void
     */
    public function critical( string|Stringable $message, array $context = [] ):void
    {
        $this->logger?->critical( $message , $context );
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     * @param string|Stringable  $message
     * @param array $context
     * @return void
     */
    public function error( string|Stringable $message, array $context = [] ):void
    {
        $this->logger?->error( $message , $context );
    }

    /**
     * Exceptional occurrences that are not errors.
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     * @param string|Stringable  $message
     * @param array $context
     * @return void
     */
    public function warning( string|Stringable $message, array $context = [] ):void
    {
        $this->logger?->warning( $message , $context );
    }

    /**
     * Normal but significant events.
     * @param string|Stringable $message
     * @param array $context
     * @return void
     */
    public function notice( string|Stringable $message , array $context = [] ):void
    {
        $this->logger?->notice( $message , $context );
    }

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     * @param string|Stringable $message
     * @param array $context
     * @return void
     */
    public function info( string|Stringable $message, array $context = [] ):void
    {
        $this->logger?->info( $message , $context );
    }

    /**
     * Detailed debug information.
     * @param string|Stringable $message
     * @param array $context
     * @return void
     */
    public function debug( string $message, array $context = [] ):void
    {
        $this->logger?->debug( $message , $context );
    }

    /**
     * Logs with an arbitrary level.
     * @param mixed   $level
     * @param string|Stringable  $message
     * @param array $context
     * @return void
     */
    public function log( mixed $level , string|Stringable $message, array $context = [] ):void
    {
        $this->logger?->log( $level , $message , $context );
    }

    /**
     * Initialize the loggable flag.
     * @param array $init
     * @return bool
     */
    protected function initLoggable( array $init = [] ) :bool
    {
        return $init[ Param::LOGGABLE ] ?? $this->loggable ;
    }

    /**
     * Initialize the logger reference.
     * @param array $init
     * @param ContainerInterface|null $container
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     * @return ?LoggerInterface
     */
    protected function initLogger( array $init = [] , ?ContainerInterface $container = null ) :?LoggerInterface
    {
        $logger = $init[ Param::LOGGER ] ?? null ;

        if( $logger instanceof LoggerInterface )
        {
            return $logger ;
        }

        if( !is_string( $logger ) || $logger == Char::EMPTY )
        {
            $logger = LoggerInterface::class ;
        }

        if( isset( $container ) && $container->has( $logger ) )
        {
            $logger = $container->get( $logger ) ;
            if( $logger instanceof LoggerInterface )
            {
                return $logger ;
            }
        }

        return null ;
    }
}