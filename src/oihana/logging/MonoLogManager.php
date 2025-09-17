<?php

namespace oihana\logging;

use Monolog\ErrorHandler;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;

use oihana\files\exceptions\DirectoryException;
use Psr\Log\LoggerInterface;

use oihana\logging\enums\MonoLogParam;

/**
 * MonoLogManager is a PSR-3 compatible logger manager using Monolog.
 *
 * This class provides:
 * - Creation of a Monolog logger with a rotating file handler.
 * - Customizable log formats and date formats.
 * - Options for inline line breaks, ignoring empty context/extra, and stack traces.
 * - Management of file and directory permissions for log storage.
 * When a log directory does not exist, it is automatically created using the configured `dirPermissions`
 * and a temporary `umask` of 0002 to ensure group writable directories (e.g., 0664 / 2775)
 * for collaborative environments.
 * - Automatic error and exception handling registration via Monolog's ErrorHandler.
 *
 * Example usage:
 * ```php
 * $loggerManager = new MonoLogManager
 * ([
 *     'directory' => '/var/log/myapp',
 *     'filePermissions' => 0664,
 *     'allowInlineLineBreaks' => true,
 *     'level' => Level::Info,
 *     'maxFiles' => 7
 * ]);
 *
 * $logger = $loggerManager->createLogger();
 * $logger->info('Application started');
 * ```
 *
 * @package oihana\logging
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class MonoLogManager extends LoggerManager
{
    /**
     * Creates a new MonoLogManager instance.
     *
     * @param array{
     *     allowInlineLineBreaks?       : bool|null   , // Whether to allow inline line breaks in log entries.
     *     bubbles?                     : bool|null   , // Indicates if the bubbling is active.
     *     directory?                   : string|null , // Base log directory.
     *     dirPermissions?              : int|null    , // Directory permissions (octal, e.g., 02775).
     *     filePermissions?             : int|null    , // File permissions (octal, e.g., 0664).
     *     extension?                   : string|null , // Log file extension (e.g., ".log").
     *     path?                        : string|null , // Subdirectory path inside $directory.
     *     dateFormat?                  : string|null , // Date format for log entries.
     *     format?                      : string|null , // Log message format string.
     *     includeStackTraces?          : bool|null   , // Whether to include exception stack traces.
     *     ignoreEmptyContextAndExtra?  : bool|null   , // Whether to ignore empty context and extra.
     *     level?                       : int|null    , // Default log level (Monolog\Level).
     *     maxFiles?                    : int|null      // Maximum number of rotating log files.
     * } $init Optional initialization options.
     *
     * @param string|null $name Optional logger channel name.
     */
    public function __construct( array $init = [] , ?string $name = null )
    {
        parent::__construct( $init , $name ) ;
        $this->allowInlineLineBreaks      = boolval( $init[ MonoLogParam::ALLOW_INLINE_LINE_BREAKS  ] ?? $this->allowInlineLineBreaks ) ;
        $this->bubbles                    = boolval( $init[ MonoLogParam::BUBBLES  ] ?? $this->bubbles ) ;
        $this->dateFormat                 = $init[ MonoLogParam::DATE_FORMAT ] ?? $this->dateFormat ;
        $this->filePermissions            = octdec( $init[ MonoLogParam::FILE_PERMISSIONS ] ?? $this->filePermissions ) ;
        $this->format                     = $init[ MonoLogParam::FORMAT ] ?? $this->format ;
        $this->includeStackTraces         = $init[ MonoLogParam::INCLUDE_STACK_TRACES ] ?? $this->includeStackTraces ;
        $this->ignoreEmptyContextAndExtra = $init[ MonoLogParam::IGNORE_EMPTY_CONTEXT_AND_EXTRA ] ?? $this->ignoreEmptyContextAndExtra ;
        $this->level                      = intval( $init[ MonoLogParam::LEVEL ] ?? $this->level ) ;
        $this->maxFiles                   = intval( $init[ MonoLogParam::MAX_FILES ] ?? $this->maxFiles ) ;
    }

    /**
     * Whether to allow inline line breaks in log entries.
     * @var bool
     */
    public bool $allowInlineLineBreaks = true ;

    /**
     * Indicates if the logger should bubble messages to higher-level loggers.
     * @var bool
     */
    public bool $bubbles = true ;

    /**
     * The date format to use in log entries.
     * Defaults to "Y-m-d H:i:s".
     * @var string
     */
    public string $dateFormat = 'Y-m-d H:i:s' ;

    /**
     * File permissions for new log files.
     * Defaults to 0664.
     * @var int|float
     */
    public int|float $filePermissions = 0664 ;

    /**
     * Format string for log messages.
     * @var string
     */
    public string $format = "%datetime% %channel% %level_name% %message% %context% %extra%\n" ;

    /**
     * Whether to include exception stack traces in log messages.
     * @var bool
     */
    public bool $includeStackTraces = false ;

    /**
     * Whether to ignore empty context and extra arrays in log messages.
     * @var bool
     */
    public bool $ignoreEmptyContextAndExtra = true ;

    /**
     * The default log level for the logger (Monolog\Level or int).
     * @var int|Level
     */
    public int|Level $level = Level::Debug ;

    /**
     * Maximum number of log files to keep in rotation.
     * @var int
     */
    public int $maxFiles = 0 ;

    /**
     * Creates and configures a logger instance.
     *
     * This method sets up a logger with a rotating file handler, custom formatter,
     * and registers it for error handling to capture system errors and exceptions.
     *
     * @return LoggerInterface The configured logger instance.
     * @throws DirectoryException If the log directory cannot be created or is not writable.
     */
    public function createLogger():LoggerInterface
    {
        // $this->ensureDirectory();

        $logger = new Logger( $this->getFileName() );

        echo 'file permissions : ' . $this->filePermissions . PHP_EOL . PHP_EOL ;

        $handler = new RotatingFileHandler
        (
            $this->getFilePath() ,
            $this->maxFiles ,
            $this->level ,
            $this->bubbles ,
            $this->filePermissions
        ) ;

        $handler->setFormatter( $this->getFormatter() );

        $logger->pushHandler( $handler );

        ErrorHandler::register( $logger ) ;

        return $logger ;
    }

    /**
     * Retrieves the formatter instance.
     * @return FormatterInterface
     */
    public function getFormatter():FormatterInterface
    {
        if ( $this->formatter === null )
        {
            $this->formatter = new LineFormatter
            (
                $this->format ,
                $this->dateFormat ,
                $this->allowInlineLineBreaks ,
                $this->ignoreEmptyContextAndExtra ,
                $this->includeStackTraces
            );
        }
        return $this->formatter ;
    }

    /**
     * Internal line formatter instance for Monolog.
     * @var ?FormatterInterface
     */
    protected ?FormatterInterface $formatter = null ;
}