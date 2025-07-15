<?php

namespace oihana\logging;

use Psr\Log\LoggerInterface ;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel ;

use oihana\enums\Char;
use oihana\traits\ToStringTrait;

use Stringable;
use Throwable;
use function oihana\core\strings\fastFormat;

/**
 * A simple PSR-3-compliant file-based logger.
 * This Logger implementation provides support for logging messages
 * to individual daily log files, categorized by severity level.
 * It supports dynamic message interpolation with context values,
 * and includes hooks for global error and exception handling.
 *
 * Example usage:
 * ```php
 * use oihana\logging\Logger;
 *
 * $logger = new Logger(__DIR__ . '/logs', Logger::DEBUG);
 *
 * $logger->info('Application started');
 * $logger->error('An error occurred: {error}', ['error' => $exception->getMessage()]);
 * ```
 *
 * Features:
 * - Supports standard PSR-3 log levels (emergency to debug)
 * - Writes logs to a file named by date (e.g., `log_2025-06-17.log`)
 * - Automatically creates the logging directory if it doesn’t exist
 * - Includes a message buffer for introspection
 * - Provides optional methods for clearing logs and capturing global errors
 *
 * Notes:
 * - This logger is not asynchronous or thread-safe.
 * - Make sure the log directory is writable by the PHP process.
 *
 * @package oihana\logging
 */
class Logger implements LoggerInterface
{
    use LoggerTrait ,
        ToStringTrait ;

    /**
     * Creates a new Logger instance.
     * @param string $directory File path to the logging directory
     * @param int $level One of the pre-defined level constants
     * @return void
     */
    public function __construct( string $directory , int $level = 7 )
    {
        $this->directory = rtrim( $directory , Char::SLASH ) ;

        if ( $level === self::OFF )
        {
            return ;
        }

        $this->path = $this->createPath( date( $this->fileDateFormat ) ) ;

        $this->severityThreshold = $level ;

        if ( !file_exists( $this->directory ) )
        {
            if( !mkdir( $this->directory, self::$_defaultPermissions, true ) )
            {
                $this->status   = self::STATUS_OPEN_FAILED ;
                $this->buffer[] = $this->getErrorMessage( self::ERROR_DIR_WRITE_FAILED , $this->directory ) ;
                return;
            }
        }

        if ( file_exists( $this->path ) && !is_writable( $this->path ) )
        {
            if ( !chmod( $this->path, 0664 ) )
            {
                $this->status   = self::STATUS_OPEN_FAILED ;
                $this->buffer[] = $this->getErrorMessage( self::ERROR_FILE_WRITE_FAILED , $this->path ) ;
                return ;
            }
        }

        if ( ( $this->_file = fopen( $this->path , 'a' ) ) )
        {
            $this->status   = self::STATUS_LOG_OPEN ;
            $this->buffer[] = $this->getErrorMessage( self::ERROR_FILE_OPEN_SUCCESS , $this->path ) ;
            if ( !chmod( $this->path, 0664 ) )
            {
                $this->buffer[] = $this->getErrorMessage( self::ERROR_FILE_PERMISSION_FAILED , $this->path , 'after opening' ) ;
            }
        }
        else
        {
            $this->status   = self::STATUS_OPEN_FAILED ;
            $this->buffer[] = $this->getErrorMessage( self::ERROR_FILE_OPEN_FAILED , $this->path ) ;
        }

    }

    /**
     * Destruct the instance.
     * @return void
     */
    public function __destruct()
    {
        if ( $this->_file )
        {
            fclose( $this->_file ) ;
        }
    }

    /////////////////////////// properties

    public string $prefix = 'log_' ;

    public string $fileDateFormat = 'Y-m-d' ;

    public string $extension = '.log' ;

    /////////////////////////// constants

    /**
     * Error severity, from low to high. From BSD syslog RFC, section 4.1.1
     * @link http://www.faqs.org/rfcs/rfc3164.html
     */
    public const int EMERGENCY  = 0 ;  // Emergency: system is unusable
    public const int ALERT      = 1 ;  // Alert: action must be taken immediately
    public const int CRITICAL   = 2 ;  // Critical: critical conditions
    public const int ERROR      = 3 ;  // Error: error conditions
    public const int WARNING    = 4 ;  // Warning: warning conditions
    public const int NOTICE     = 5 ;  // Notice: normal but significant condition
    public const int INFO       = 6 ;  // Informational: informational messages
    public const int DEBUG      = 7 ;  // Debug: debug messages
    public const int OFF        = 8 ;  // Log nothing at all

    public const string ERROR_DIR_WRITE_FAILED       = 'dir:write:failed' ;
    public const string ERROR_FILE_OPEN_FAILED       = 'fil:open:failed'  ;
    public const string ERROR_FILE_OPEN_SUCCESS      = 'fil:open:success' ;
    public const string ERROR_FILE_PERMISSION_FAILED = 'fil:permission:failed' ;
    public const string ERROR_FILE_WRITE_FAILED      = 'fil:write:failed' ;

    /**
     * Internal status codes
     */
    public const int STATUS_LOG_OPEN    = 1 ;
    public const int STATUS_OPEN_FAILED = 2 ;
    public const int STATUS_LOG_CLOSED  = 3 ;

    /**
     * @var array
     */
    public const array LEVELS =
    [
        LogLevel::EMERGENCY => self::EMERGENCY,
        LogLevel::ALERT     => self::ALERT,
        LogLevel::CRITICAL  => self::CRITICAL,
        LogLevel::ERROR     => self::ERROR,
        LogLevel::WARNING   => self::WARNING,
        LogLevel::NOTICE    => self::NOTICE,
        LogLevel::INFO      => self::INFO,
        LogLevel::DEBUG     => self::DEBUG
    ];

    /**
     * Creates the log file path with a specific name.
     * @param string $name
     * @return string
     */
    public function createPath( string $name ) : string
    {
        return $this->directory . DIRECTORY_SEPARATOR . $this->prefix . $name . $this->extension ;
    }

    /**
     * Writes a message to the log with the given severity.
     * @param int $level Severity level of log message (use constants)
     * @param string|Stringable $message Text to add to the log
     * @param array $context
     * @return void
     */
    public function log( mixed $level , string|Stringable $message , array $context = [] ):void
    {
        $num = self::LEVELS[ $level ] ?? self::OFF ;

        if ( $this->severityThreshold >= $num )
        {
            // ------- status

            $status = date( self::$_dateFormat ) . Char::SPACE ;

            if( array_key_exists( $level , self::LEVELS ) )
            {
                $status .= strtoupper( $level ) ;
            }

            // ------- message

            $message = $status . Char::SPACE . $message ;

            if( is_array( $context ) && count( $context ) > 0 )
            {
                $replace = [] ;
                foreach ( $context as $key => $value )
                {
                    if ( !is_array($value) && (!is_object($value) || method_exists( $value, '__toString') ) )
                    {
                        $replace[ Char::LEFT_BRACE . $key . Char::RIGHT_BRACE ] = $value ;
                    }
                }
                $message = strtr( $message , $replace ) ;
            }

            // ------- write

            $this->writeFreeFormLine( $message ) ;
        }
    }

    /////////////////////////// global error callback methods

    /**
     * Invoked when a global error is sending.
     */
    public function onError( string|int $code , string $message , string|int $file , string|int $line , string $pattern = '[%s] - L:%s - C:%s - %s' ):true
    {
        $this->error( sprintf( $pattern , $file , $line , $code , $message ) ) ;
        return true ;
    }

    /**
     * Invoked when a global exception is sending.
     */
    public function onException( Throwable $exception ):true
    {
        $this->critical( Char::LEFT_BRACKET . get_class($exception) . Char::RIGHT_BRACKET . Char::SPACE . $exception->getMessage() ) ;
        return true ;
    }

    ///////////////////////////

    /**
     * Clear all logs in the log path.
     */
    public function clear() :void
    {
        $logs = $this->getLogFiles() ;
        foreach( $logs as $log )
        {
            unlink( $this->directory . Char::SLASH . $log ) ;
        }
    }

    /**
     * Returns the list of all log files in the logger directory.
     * @return array
     */
    public function getLogFiles():array
    {
        return array_values
        (
            array_filter
            (
                scandir( $this->directory ) ,
                fn( $file ) => $file != Char::DOT && $file != Char::DOUBLE_DOT
            )
        ) ;
    }

    /**
     * Returns (and removes) the last message from the queue buffer.
     * @return string
     */
    public function getMessage() :string
    {
        return array_pop( $this->buffer ) ;
    }

    /**
     * Returns the entire message queue (leaving it intact)
     * @return array
     */
    public function getErrors() : array
    {
        return $this->buffer ;
    }

    /**
     * Returns the directory of the logs files.
     * @return string
     */
    public function getDirectory() : string
    {
        return $this->directory ;
    }

    /**
     * Returns the path of the logs files.
     * @return string
     */
    public function getPath() :string
    {
        return $this->path ;
    }

    /**
     * Indicates the status of the logger instance.
     */
    public function getStatus() : int
    {
        return $this->status ;
    }

    /**
     * Writes a line to the log without prepending a status or timestamp.
     * @param string $line Line to write to the log
     * @return void
     */
    public function writeFreeFormLine( string $line ) :void
    {
        if ( $this->status == self::STATUS_LOG_OPEN && $this->severityThreshold != self::OFF )
        {
            if ( fwrite( $this->_file , $line . PHP_EOL ) === false )
            {
                $this->buffer[] = $this->getErrorMessage( self::ERROR_FILE_WRITE_FAILED , $this->path ) ;
            }
        }
    }

    ///////////////////////////

    /**
     * Holds messages generated by the class
     * @var array
     */
    private array $buffer = [];

    /**
     * Default severity of log messages, if not specified
     * @var int
     */
    private static int $_defaultSeverity = self::DEBUG ;

    /**
     * Valid PHP date() format string for log timestamps
     * @var string
     */
    private static string $_dateFormat = 'Y-m-d H:i:s';

    /**
     * Octal notation for default permissions of the log file
     * @var int
     */
    private static int $_defaultPermissions = 0775 ;

    /**
     * The directory to the log file
     * @var string
     */
    private string $directory ;

    /**
     * This holds the file handle for this instance's log file
     * @var resource
     */
    private $_file = null ;


    /**
     * Standard messages produced by the class. Can be modified for il8n
     * @var array
     */
    private array $errors =
    [
        self::ERROR_DIR_WRITE_FAILED       => 'Failed to create log directory: {0}' ,
        self::ERROR_FILE_OPEN_SUCCESS      => 'The log file was opened successfully.',
        self::ERROR_FILE_OPEN_FAILED       => 'The file could not be opened. Check permissions.' ,
        self::ERROR_FILE_PERMISSION_FAILED => 'Failed to set correct permissions (0664) on log file {0} {1}.',
        self::ERROR_FILE_WRITE_FAILED      => 'The file {0} could not be written to. Failed to change permissions for existing file, check that appropriate permissions have been set.',
    ];


    /**
     * Path to the log file
     * @var ?string
     */
    private ?string $path = null ;

    /**
     * Current minimum logging threshold
     * @var int
     */
    private int $severityThreshold = self::DEBUG ;

    /**
     * Current status of the log file
     * @var int
     */
    private int $status = self::STATUS_LOG_CLOSED ;

    /**
     * Returns an error message with a specific error status.
     * @param string $errorStatus
     * @param ...$args
     * @return string
     */
    protected function getErrorMessage( string $errorStatus , ...$args ):string
    {
        if( isset( $this->errors[ $errorStatus ]  ) )
        {
            return fastFormat( $this->errors[ $errorStatus ] , ...$args ) ;
        }
        return Char::EMPTY ;
    }
}