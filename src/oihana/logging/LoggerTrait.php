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

/**
 * Provides PSR-3 logging capabilities to any class via {@see LoggerAwareTrait}.
 *
 * This trait offers a complete set of logging methods corresponding to all PSR-3 log levels,
 * along with utilities to initialize and configure a logger instance from various sources.
 *
 * Key features:
 * - PSR-3 compliant logging methods: {@see emergency()}, {@see alert()}, {@see critical()},
 *   {@see error()}, {@see warning()}, {@see notice()}, {@see info()}, {@see debug()}, and {@see log()}.
 * - Constants {@see LOGGER} and {@see LOGGABLE} to standardize logger-related configuration keys.
 * - Initialization methods:
 *   - {@see initLoggable()} to configure whether logging is enabled or disabled.
 *   - {@see initLogger()} to set a logger instance directly, via an associative array, or by resolving
 *     a service from a PSR-11 container.
 * - Support for optional dependency injection container resolution, allowing the logger to be fetched
 *   from a DI container by service ID or class name.
 *
 * Usage:
 * - Include this trait in a class to gain PSR-3 logging support without manually implementing
 *   {@see LoggerInterface}.
 * - Optionally, call {@see initLogger()} during construction or setup to bind the desired logger.
 * - Use {@see loggable} to control whether log calls should be performed.
 *
 * Example:
 * ```php
 * use DI\Container;
 * use Psr\Log\LoggerInterface;
 * use oihana\logging\LoggerTrait;
 *
 * class MyService
 * {
 *     use LoggerTrait;
 *
 *     public function __construct( ?LoggerInterface $logger = null, ?Container $container = null )
 *     {
 *          $this->initLoggable(['loggable' => true]);
 *          $this->initLogger($logger ?? ['logger' => 'my_logger_service'], $container);
 *     }
 *
 *     public function run(): void
 *     {
 *          if ($this->loggable)
 *          {
 *               $this->info('Service started.');
 *              try
 *              {
 *                  // perform some action
 *              } catch ( Throwable $e )
 *              {
 *                  $this->error('An error occurred', ['exception' => $e]);
 *              }
 *          }
 *      }
 * }
 * ```
 *
 * @package oihana\logging
 *
 * @see LoggerInterface
 * @see LoggerAwareTrait
 * @see ContainerInterface
 */
trait LoggerTrait
{
    use LoggerAwareTrait ;

    /**
     * The 'logger' parameter constant.
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
     * Returns the logger reference.
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
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
     * Initialize the loggable flag.
     * @param bool|array|null $init         The definition to initialize the loggable property.
     * @param bool|array|null $defaultValue The default value if the $init argument is not defined.
     * @return static
     */
    public function initLoggable( bool|array|null $init = null , bool $defaultValue = false ) :static
    {
        $this->loggable = match( true )
        {
            is_bool  ( $init ) => $init,
            is_array ( $init ) => $init[ static::LOGGABLE ] ?? $defaultValue ,
            default            => $defaultValue ,
        };
        return $this;
    }

    /**
     * Initializes the logger reference for the current instance.
     *
     * This method accepts either:
     * - A {@see LoggerInterface} instance
     * - An associative array containing a logger reference under the {@see static::LOGGER} key
     * - A string representing a service ID or class name resolvable by the container
     * - `null` or an empty value, which will default to {@see LoggerInterface::class}
     *
     * If a dependency injection container is provided, the method will attempt to
     * resolve the logger service from it. If no valid logger can be resolved, the
     * `$this->logger` property will be set to `null`.
     *
     * @param array|LoggerInterface|null $init      Logger initialization data.
     *                                              May be an instance, an array with a logger entry,
     *                                              a string service ID/class name, or `null`.
     * @param ContainerInterface|null    $container Optional dependency injection container
     *                                              used to resolve the logger service.
     *
     * @throws ContainerExceptionInterface If the container encounters a general error.
     * @throws DependencyException         If there is a dependency resolution error.
     * @throws NotFoundException           If the specified service is not found in the container.
     * @throws NotFoundExceptionInterface  If the specified service is not found in the container.
     *
     * @return static Returns the current instance for method chaining.
     */
    public function initializeLogger
    (
        array|LoggerInterface|null $init      = null ,
        ?ContainerInterface        $container = null
    )
    :static
    {
        if ( $init instanceof LoggerInterface )
        {
            $this->logger = $init;
            return $this ;
        }

        $logger = is_array( $init ) ? ( $init[ static::LOGGER ] ?? null ) : $init ;

        if ( !is_string($logger) || trim($logger) === '' )
        {
            $logger = LoggerInterface::class ;
        }

        if ( $container?->has( $logger ) )
        {
            $entry = $container->get($logger) ;
            if ( $entry instanceof LoggerInterface)
            {
                $this->logger = $entry ;
                return $this ;
            }
        }

        $this->logger = null;
        return $this;
    }

    /**
     * Detailed debug information.
     * @param string|Stringable $message
     * @param array $context
     * @return void
     */
    public function debug( string|Stringable $message, array $context = [] ):void
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
}