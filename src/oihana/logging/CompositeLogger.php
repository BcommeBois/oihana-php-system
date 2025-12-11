<?php

namespace oihana\logging;

use Psr\Log\LoggerInterface;
use Stringable;
use WeakMap;

/**
 * A simple composite logger that broadcasts log messages to multiple logger instances.
 *
 * This class implements the Composite pattern with a minimal API, allowing you to
 * add multiple PSR-3 loggers and broadcast all log calls to each registered logger.
 *
 * Uses WeakMap (PHP 8.4+) to automatically remove loggers when they are garbage collected.
 *
 * Example:
 * ```php
 * $fileLogger = new FileLogger('/var/log/app.log');
 * $emailLogger = new EmailLogger('admin@example.com');
 *
 * $composite = new CompositeLogger([$fileLogger, $emailLogger]);
 *
 * // This will log to both file and email
 * $composite->error('Database connection failed', ['host' => 'localhost']);
 *
 * // Remove a specific logger by reference
 * $composite->removeLogger($emailLogger);
 *
 * // Logger is automatically removed when no external reference exists
 * unset($emailLogger); // Automatically removed from composite
 * ```
 *
 * @package oihana\logging
 */
class CompositeLogger implements LoggerInterface
{
    /**
     * Constructor optionally accepting an array of loggers.
     *
     * @param LoggerInterface[] $loggers Initial loggers to register
     */
    public function __construct( array $loggers = [] )
    {
        $this->loggers = new WeakMap();
        foreach ( $loggers as $logger )
        {
            if ( $logger instanceof LoggerInterface )
            {
                $this->addLogger( $logger ) ;
            }
        }
    }

    /**
     * Indicates the number of registered loggers.
     * @return int
     */
    public int $count
    {
        get => $this->loggers->count() ;
    }

    /**
     * Adds a logger to the composite.
     *
     * @param LoggerInterface $logger The logger instance to add
     *
     * @return static Returns the current instance for method chaining
     */
    public function addLogger( LoggerInterface $logger ) :static
    {
        if( !isset( $this->loggers[ $logger ] ) )
        {
            $this->loggers[ $logger ] = true;
        }
        return $this;
    }

    /**
     * Removes all registered loggers.
     *
     * @return static Returns the current instance for method chaining
     */
    public function clear(): static
    {
        $this->loggers = new WeakMap() ;
        return $this;
    }

    /**
     * Returns all registered loggers.
     * Note: Returns a new array of active loggers at the time of call.
     *
     * @return LoggerInterface[]
     */
    public function getLoggers() : array
    {
        $result = [] ;
        foreach ( $this->loggers as $logger => $value )
        {
            $result[] = $logger;
        }
        return $result ;
    }

    /**
     * Checks if a logger is registered in the composite.
     *
     * @param LoggerInterface $logger The logger instance to check
     *
     * @return bool True if the logger is registered, false otherwise
     */
    public function hasLogger( LoggerInterface $logger ): bool
    {
        return isset( $this->loggers[ $logger ] ) ;
    }

    /**
     * Removes a logger from the composite by reference.
     *
     * @param LoggerInterface $logger The logger instance to remove
     *
     * @return static Returns the current instance for method chaining
     */
    public function removeLogger( LoggerInterface $logger ): static
    {
        if( isset( $this->loggers[ $logger ] ) )
        {
            unset( $this->loggers[ $logger ] ) ;
        }
        return $this ;
    }

    /**
     * {@inheritdoc}
     */
    public function emergency( string|Stringable $message, array $context = [] ) :void
    {
        foreach ( $this->loggers as $logger => $value )
        {
            $logger->emergency( $message , $context ) ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function alert( string|Stringable $message , array $context = [] ) :void
    {
        foreach ( $this->loggers as $logger => $value )
        {
            $logger->alert( $message , $context ) ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function critical( string|Stringable $message , array $context = [] ) :void
    {
        foreach ( $this->loggers as $logger => $value )
        {
            $logger->critical( $message , $context ) ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function error( string|Stringable $message , array $context = [] ) :void
    {
        foreach ( $this->loggers as $logger => $value )
        {
            $logger->error( $message , $context ) ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function warning( string|Stringable $message, array $context = [] ) :void
    {
        foreach ( $this->loggers as $logger => $value )
        {
            $logger->warning( $message , $context ) ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function notice( string|Stringable $message, array $context = [] ) :void
    {
        foreach ( $this->loggers as $logger => $value )
        {
            $logger->notice( $message , $context ) ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function info( string|Stringable $message, array $context = [] ) :void
    {
        foreach ( $this->loggers as $logger => $value )
        {
            $logger->info( $message, $context ) ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function debug( string|Stringable $message, array $context = [] ) :void
    {
        foreach ( $this->loggers as $logger => $value )
        {
            $logger->debug( $message , $context ) ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function log( $level , string|Stringable $message , array $context = [] ) :void
    {
        foreach ( $this->loggers as $logger => $value )
        {
            $logger->log( $level , $message , $context ) ;
        }
    }

    /**
     * WeakMap storing loggers with automatic garbage collection.
     * @var WeakMap<LoggerInterface, true>
     */
    private WeakMap $loggers ;
}