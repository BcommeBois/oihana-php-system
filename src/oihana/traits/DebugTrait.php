<?php

namespace oihana\traits;

use oihana\logging\LoggerTrait;

/**
 * Provides debugging and mock-related functionality for classes.
 *
 * This trait allows a class to handle:
 *  - A debug mode, indicating if debug features should be enabled.
 *  - A mock mode, used for testing or simulating data, which is only active
 *    if debug mode is active.
 *
 * Both modes can be initialized via arrays using the constants `DEBUG` and `MOCK`.
 * Any non-boolean value provided will fall back to a default value.
 *
 * Example usage:
 *
 * ```php
 * class MyService
 * {
 *     use DebugTrait;
 * }
 *
 * $service = (new MyService())
 *     ->initializeDebug([DebugTrait::DEBUG => true])
 *     ->initializeMock([DebugTrait::MOCK => true]);
 *
 * var_dump($service->isDebug()); // true
 * var_dump($service->isMock());  // true
 *
 * // Non-boolean values will use the default
 * $service->initializeDebug([DebugTrait::DEBUG => 'yes'], false);
 * var_dump($service->isDebug()); // false
 * ```
 *
 * @package oihana\traits
 */
trait DebugTrait
{
    use LoggerTrait ;

    /**
     * Indicates if use the debug mode.
     * @var bool
     */
    public bool $debug = false ;

    /**
     * The mock flag to test the model.
     * @var bool
     */
    public bool $mock = false ;

    /**
     * The 'debug' parameter constant.
     */
    public const string DEBUG = 'debug' ;

    /**
     * The 'mock' parameter constant.
     */
    public const string MOCK = 'mock' ;

    /**
     * Initialize the debug flag.
     *
     * This method sets the `$debug` property using the value provided in the `$init` array,
     * or falls back to the current `$debug` value. If the value in `$init` is not a boolean,
     * the provided `$defaultValue` is used instead.
     *
     * @param array $init         Optional initialization array.
     * @param bool  $defaultValue Default value to use if the init value is not a boolean.
     * @return static             Returns the current instance for chaining.
     */
    public function initializeDebug( array $init = [] , bool $defaultValue = false ):static
    {
        $val = array_key_exists( static::DEBUG , $init ) ? $init[ static::DEBUG ] : $this->debug;
        $this->debug = is_bool( $val ) ? $val : $defaultValue ;
        return $this ;
    }

    /**
     * Initialize the mock flag.
     *
     * This method sets the `$mock` property using the value provided in the `$init` array,
     * or falls back to the current `$mock` value. If the value in `$init` is not a boolean,
     * the provided `$defaultValue` is used instead.
     *
     * @param array $init         Optional initialization array.
     * @param bool  $defaultValue Default value to use if the init value is not a boolean.
     * @return static             Returns the current instance for chaining.
     */
    public function initializeMock( array $init = [] , bool $defaultValue = false ):static
    {
        $val = array_key_exists( static::MOCK , $init ) ? $init[ static::MOCK ] : $this->mock;
        $this->mock = is_bool($val) ? $val : $defaultValue ;
        return $this;
    }

    /**
     * Check if debug mode is active.
     *
     * This method returns the boolean value of the debug flag. If a value is provided
     * in the `$init` array, it is used; otherwise the current `$debug` property is used.
     * Non-boolean values in `$init` are treated as `false`.
     *
     * @param array $init Optional array containing a debug value.
     * @return bool       True if debug mode is active, false otherwise.
     */
    public function isDebug( array $init = [] ) :bool
    {
        $val = $init[ static::DEBUG ] ?? $this->debug;
        return is_bool( $val ) && $val ;
    }

    /**
     * Check if mock mode is active.
     *
     * Mock mode is only active if debug mode is active as well.
     * If a value is provided in the `$init` array, it is used; otherwise the current `$mock` property is used.
     * Non-boolean values in `$init` are treated as `false`.
     *
     * @param array $init Optional array containing a mock value.
     * @return bool       True if mock mode is active, false otherwise.
     */
    public function isMock( array $init = [] ) :bool
    {
        $val = $init[ static::MOCK ] ?? $this->mock;
        return $this->isDebug( $init ) && is_bool( $val ) && $val ;
    }
}