<?php

namespace oihana\signals;

/**
 * Internal container for signal receiver metadata.
 *
 * This class wraps a receiver callable along with its execution priority
 * and auto-disconnect behavior. It is used internally by the Signal class
 * to manage and order receivers.
 *
 * SignalEntry instances are created automatically when connecting receivers
 * to a Signal and should not be instantiated directly by user code.
 *
 * @internal This class is part of the internal signal implementation.
 *
 * @example Internal usage by Signal class
 * ```php
 * // This happens internally in Signal::connect()
 * $entry = new SignalEntry(
 *     receiver: fn() => echo 'test',
 *     priority: 10,
 *     auto: false
 * );
 * ```
 *
 * @author Marc Alcaraz (ekameleon)
 * @since 1.0.0
 * @package oihana\signals
 */
class SignalEntry
{
    /**
     * Creates a new SignalEntry instance.
     *
     * This constructor wraps a receiver callable with its associated metadata
     * for priority-based execution and lifecycle management.
     *
     * @param callable $receiver The receiver callable to invoke when the signal is emitted.
     *                           Can be any valid PHP callable (closure, arrow function,
     *                           array callable, function name, etc.)
     *
     * @param int $priority The execution priority (higher values execute first).
     *                      Default: 0
     *                      - Positive values: High priority (execute early)
     *                      - Zero: Normal priority
     *                      - Negative values: Low priority (execute late)
     *
     * @param bool $auto Auto-disconnect flag. If true, the receiver is automatically
     *                   disconnected after its first execution. Default: false
     *                   - true: One-time listener (disconnects after first emit)
     *                   - false: Persistent listener (remains connected)
     *
     * @example
     * ```php
     * // Normal persistent receiver with default priority
     * $entry1 = new SignalEntry( fn() => echo 'normal' );
     *
     * // High-priority receiver
     * $entry2 = new SignalEntry(
     *     receiver: fn() => echo 'urgent',
     *     priority: 100
     * );
     *
     * // One-time receiver
     * $entry3 = new SignalEntry(
     *     receiver: fn() => echo 'once',
     *     auto: true
     * );
     * ```
     */
    public function __construct( callable $receiver , int $priority = 0 , bool $auto = false )
    {
        $this->auto     = $auto ;
        $this->priority = $priority ;
        $this->receiver = $receiver ;
    }

    /**
     * Auto-disconnect flag.
     *
     * When true, this receiver will be automatically removed from the signal
     * after it executes once. This is useful for one-time event listeners
     * that should only respond to the first emission.
     *
     * @var bool
     *
     * @example
     * ```php
     * $entry = new SignalEntry( fn() => echo 'init', auto: true );
     *
     * if ( $entry->auto ) {
     *     echo 'This receiver will disconnect after first use';
     * }
     * ```
     */
    public bool $auto = false ;

    /**
     * Execution priority value.
     *
     * Determines the order in which receivers are called when a signal is emitted.
     * Higher priority values execute before lower ones.
     *
     * Priority scale:
     * - 100+  : Critical/urgent handlers (security, validation)
     * - 10-99 : High priority handlers (logging, monitoring)
     * - 0     : Normal priority (default, most handlers)
     * - -1 to -99 : Low priority (cleanup, optional tasks)
     * - -100- : Deferred handlers (non-critical processing)
     *
     * @var int
     *
     * @example
     * ```php
     * $critical = new SignalEntry( fn() => validate(), priority: 100 );
     * $logger   = new SignalEntry( fn() => log(), priority: 10 );
     * $normal   = new SignalEntry( fn() => process() );
     * $cleanup  = new SignalEntry( fn() => clean(), priority: -10 );
     *
     * // Execution order: validate() -> log() -> process() -> clean()
     * ```
     */
    public int $priority = 0 ;

    /**
     * The receiver callable.
     *
     * Stores the actual callable that will be invoked when the signal is emitted.
     * This can be any valid PHP callable, including:
     * - Closures: `function() { ... }`
     * - Arrow functions: `fn() => ...`
     * - Array callables: `[$object, 'method']` or `[ClassName::class, 'staticMethod']`
     * - Function names: `'functionName'`
     * - Invokable objects: `new InvokableClass()`
     *
     * The type is `mixed` to accommodate all PHP callable formats, but it
     * is always enforced to be callable by the constructor.
     *
     * @var mixed Internally stores a callable, but typed as mixed for flexibility.
     *
     * @example
     * ```php
     * // Closure
     * $entry1 = new SignalEntry( function($data) { echo $data; } );
     *
     * // Arrow function
     * $entry2 = new SignalEntry( fn($x) => $x * 2 );
     *
     * // Array callable (object method)
     * $handler = new MyHandler();
     * $entry3 = new SignalEntry( [$handler, 'handle'] );
     *
     * // Call the receiver
     * call_user_func_array( $entry1->receiver, ['Hello'] );
     * ```
     */
    public mixed $receiver ;
}