<?php

namespace oihana\signals;

/**
 * A fast and flexible signal/slot implementation for event-driven programming.
 *
 * This class provides a robust observer pattern implementation with priority-based
 * execution, auto-disconnect capability, and support for both callable functions
 * and Receiver objects.
 *
 * Features:
 * - Priority-based receiver execution (higher priority executes first)
 * - Auto-disconnect for one-time listeners
 * - Type-safe receiver management
 * - Efficient sorting and execution
 *
 * @example Basic usage with callables and Receiver objects
 * ```php
 * use oihana\signals\Signal;
 * use oihana\signals\Receiver;
 *
 * // Define a Receiver class
 * class NotificationHandler implements Receiver
 * {
 *     public function receive( mixed ...$values ) :void
 *     {
 *         echo 'Notification: ' . implode(', ', $values) . PHP_EOL;
 *     }
 * }
 *
 * // Create receivers
 * $logger = function( mixed ...$values )
 * {
 *     echo 'Log: ' . implode(', ', $values) . PHP_EOL;
 * };
 *
 * $handler = new NotificationHandler();
 *
 * // Setup signal
 * $signal = new Signal();
 *
 * // Connect with different priorities
 * $signal->connect( $logger  , priority: 10 ); // Executes first
 * $signal->connect( $handler , priority: 5 ); // Executes second
 *
 * // Emit values to all connected receivers
 * $signal->emit( 'User logged in', 'user123' );
 *
 * // One-time listener
 * $signal->connect
 * (
 *     fn() => echo 'First emit only!' . PHP_EOL,
 *     autoDisconnect: true
 * );
 * ```
 *
 * @example Advanced usage with priority and auto-disconnect
 * ```php
 * $signal = new Signal();
 *
 * // High priority handler (executes first)
 * $signal->connect
 * (
 *     fn($msg) => echo "URGENT: $msg" . PHP_EOL,
 *     priority: 100
 * );
 *
 * // One-time handler (disconnects after first emit)
 * $signal->connect
 * (
 *     fn($msg) => echo "Initialization: $msg" . PHP_EOL,
 *     priority: 50,
 *     autoDisconnect: true
 * );
 *
 * // Normal priority handler
 * $signal->connect
 * (
 *     fn($msg) => echo "Info: $msg" . PHP_EOL
 * );
 *
 * // First emit - all three handlers execute
 * $signal->emit('System started');
 *
 * // Second emit - only two handlers execute (auto-disconnect removed one)
 * $signal->emit('Processing data');
 * ```
 *
 * @author Marc Alcaraz (ekameleon)
 * @since 1.0.0
 * @package oihana\signals
 */
class Signal implements Signaler
{
    /**
     * Creates a new Signal instance.
     *
     * @param array<callable|Receiver>|null $receivers Optional array of initial receivers to connect.
     *                                                  Each receiver will be connected with default priority (0).
     *
     * @example
     * ```php
     * // Create empty signal
     * $signal = new Signal();
     *
     * // Create signal with initial receivers
     * $signal = new Signal([
     *     fn() => echo 'Handler 1',
     *     fn() => echo 'Handler 2'
     * ]);
     * ```
     */
    public function __construct( ?array $receivers = null )
    {
        $this->receivers = [] ;
        if ( !empty( $receivers ) )
        {
            foreach( $receivers as $receiver )
            {
                $this->connect( $receiver ) ;
            }
        }
    }

    /**
     * The method name used when calling Receiver objects.
     */
    public const string RECEIVE = 'receive' ;

    /**
     * The number of currently connected receivers.
     *
     * @var int Read-only property that returns the count of active receivers.
     *
     * @example
     * ```php
     * $signal = new Signal();
     * echo $signal->length; // 0
     *
     * $signal->connect( fn() => echo 'test' );
     * echo $signal->length; // 1
     * ```
     */
    public int $length
    {
        get => count( $this->receivers ) ;
    }

    /**
     * Connects a receiver to this signal.
     *
     * Receivers are executed in order of priority (highest first). If the same
     * receiver is already connected, this method returns false and does not
     * create a duplicate connection.
     *
     * @param callable|Receiver $receiver The receiver to connect. Can be:
     *                                     - A callable (function, closure, arrow function)
     *                                     - An object implementing the Receiver interface
     * @param int $priority Execution priority (higher values execute first). Default: 0
     * @param bool $autoDisconnect If true, receiver is automatically disconnected after first emit. Default: false
     *
     * @return bool True if the receiver was successfully connected, false if:
     *              - The receiver is already connected
     *              - The receiver is not callable and not a Receiver object
     *
     * @example
     * ```php
     * $signal = new Signal();
     *
     * // Connect a closure
     * $signal->connect( fn($data) => echo $data );
     *
     * // Connect with high priority
     * $signal->connect( fn() => echo 'First!', priority: 100 );
     *
     * // Connect a one-time listener
     * $signal->connect(
     *     fn() => echo 'Once only',
     *     autoDisconnect: true
     * );
     *
     * // Duplicate connection returns false
     * $handler = fn() => echo 'test';
     * $signal->connect( $handler ); // true
     * $signal->connect( $handler ); // false (already connected)
     * ```
     */
    public function connect( mixed $receiver , int $priority = 0 , bool $autoDisconnect = false ) :bool
    {
        if ( is_callable( $receiver ) || ( $receiver instanceof Receiver ) )
        {
            if ( $receiver instanceof Receiver )
            {
                $receiver = [ $receiver , self::RECEIVE ] ;
            }

            if ( $this->hasReceiver( $receiver ) )
            {
                return false ;
            }

            $this->receivers[] = new SignalEntry( $receiver , $priority , $autoDisconnect ) ;

            usort($this->receivers, fn( $a , $b ) => $b->priority <=> $a->priority ) ;

            return true ;
        }
        else
        {
            return false ;
        }
    }

    /**
     * Checks if any receivers are connected to this signal.
     *
     * @return bool True if at least one receiver is connected, false otherwise.
     *
     * @example
     * ```php
     * $signal = new Signal();
     *
     * if ( !$signal->connected() ) {
     *     echo 'No listeners attached';
     * }
     *
     * $signal->connect( fn() => echo 'test' );
     *
     * if ( $signal->connected() ) {
     *     $signal->emit( 'data' ); // Safe to emit
     * }
     * ```
     */
    public function connected():bool
    {
        return count( $this->receivers ) > 0 ;
    }

    /**
     * Disconnects one or all receivers from this signal.
     *
     * @param callable|Receiver|null $receiver The receiver to disconnect.
     *                                         If null, disconnects ALL receivers.
     *                                         If specified, disconnects only that receiver.
     *
     * @return bool True if:
     *              - A specific receiver was found and disconnected
     *              - All receivers were disconnected (when $receiver is null and receivers exist)
     *              False if:
     *              - The specified receiver was not found
     *              - No receivers exist when trying to disconnect all
     *
     * @example
     * ```php
     * $signal = new Signal();
     * $handler1 = fn() => echo 'A';
     * $handler2 = fn() => echo 'B';
     *
     * $signal->connect( $handler1 );
     * $signal->connect( $handler2 );
     *
     * // Disconnect specific receiver
     * $signal->disconnect( $handler1 ); // true
     * $signal->disconnect( $handler1 ); // false (already disconnected)
     *
     * // Disconnect all receivers
     * $signal->disconnect(); // true
     * $signal->disconnect(); // false (no receivers to disconnect)
     * ```
     */
    public function disconnect( mixed $receiver = null ) :bool
    {
        if ( !isset( $receiver ) )
        {
            if ( count( $this->receivers ) > 0 )
            {
                $this->receivers = [] ;
                return true ;
            }
            else
            {
                return false  ;
            }
        }

        if ( $receiver instanceof Receiver )
        {
            $receiver = [ $receiver , self::RECEIVE ]  ;
        }

        if ( is_callable( $receiver ) && count( $this->receivers ) > 0 )
        {
            foreach( $this->receivers as $key => $entry )
            {
                if( $entry->receiver === $receiver )
                {
                    unset( $this->receivers[ $key ] ) ;
                    $this->receivers = array_values( $this->receivers );
                    return true ;
                }
            }
        }

        return false ;
    }

    /**
     * Emits a signal, calling all connected receivers with the provided values.
     *
     * Receivers are called in priority order (highest to lowest). Receivers marked
     * with autoDisconnect are automatically removed after being called.
     *
     * @param mixed ...$values Zero or more values to pass to each receiver.
     *                         All receivers receive the same values.
     *
     * @return void
     *
     * @example
     * ```php
     * $signal = new Signal();
     *
     * $signal->connect( fn($name, $age) =>
     *     echo "User: $name, Age: $age" . PHP_EOL
     * );
     *
     * // Emit with multiple parameters
     * $signal->emit( 'Alice', 30 );
     * // Output: User: Alice, Age: 30
     *
     * // Emit with no parameters
     * $signal->connect( fn() => echo 'Triggered!' . PHP_EOL );
     * $signal->emit();
     * // Output: Triggered!
     *
     * // Auto-disconnect example
     * $signal->connect(
     *     fn() => echo 'Once!' . PHP_EOL,
     *     autoDisconnect: true
     * );
     * $signal->emit(); // Prints "Once!"
     * $signal->emit(); // Doesn't print (already disconnected)
     * ```
     */
    public function emit( mixed ...$values ):void
    {
        if ( count( $this->receivers ) == 0 )
        {
            return ;
        }

        $toRemove = [];
        foreach( $this->receivers as $key => $entry )
        {
            call_user_func_array( $entry->receiver , $values ) ;
            if ( $entry->auto )
            {
                $toRemove[] = $key ;
            }
        }

        if( !empty( $toRemove ) )
        {
            foreach( $toRemove as $key )
            {
                unset( $this->receivers[ $key ] ) ;
            }

            $this->receivers = array_values( $this->receivers );
        }
    }

    /**
     * Checks if a specific receiver is connected to this signal.
     *
     * @param callable|Receiver $receiver The receiver to search for.
     *
     * @return bool True if the receiver is connected, false otherwise.
     *
     * @example
     * ```php
     * $signal = new Signal();
     * $handler = fn() => echo 'test';
     *
     * echo $signal->hasReceiver( $handler ); // false
     *
     * $signal->connect( $handler );
     * echo $signal->hasReceiver( $handler ); // true
     *
     * $signal->disconnect( $handler );
     * echo $signal->hasReceiver( $handler ); // false
     * ```
     */
    public function hasReceiver( mixed $receiver ) :bool
    {
        if ( $receiver instanceof Receiver )
        {
            $receiver = array( $receiver , 'receive' ) ;
        }

        if ( !is_callable( $receiver ) || empty( $this->receivers ) )
        {
            return false ;
        }

        if ( array_any( $this->receivers , fn( $entry ) => $entry->receiver === $receiver ) )
        {
            return true ;
        }

        return false ;
    }

    /**
     * Returns an array of all connected receivers.
     *
     * The returned array contains the actual callable references, not the
     * SignalEntry wrapper objects.
     *
     * @return array<callable> Array of receiver callables in priority order.
     *
     * @example
     * ```php
     * $signal = new Signal();
     * $handler1 = fn() => echo 'A';
     * $handler2 = fn() => echo 'B';
     *
     * $signal->connect( $handler1, priority: 10 );
     * $signal->connect( $handler2, priority: 5 );
     *
     * $receivers = $signal->toArray();
     * // Returns: [$handler1, $handler2] (in priority order)
     *
     * echo count($receivers); // 2
     * ```
     */
    public function toArray() :array
    {
        $ar = [] ;
        if ( count( $this->receivers ) > 0 )
        {
            foreach ( $this->receivers as $entry )
            {
                $ar[] = $entry->receiver ;
            }
        }
        return $ar ;
    }

    /**
     * Internal storage for connected receivers.
     *
     * Each element is a SignalEntry object containing the receiver callable,
     * its priority, and auto-disconnect flag.
     *
     * @var array<SignalEntry>
     */
    protected array $receivers ;
}