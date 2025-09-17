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

use oihana\enums\Char;
use oihana\logging\enums\MonoLogParam;

/**
 * A MonoLog logger manager.
 */
class MonoLogManager extends LoggerManager
{
    /**
     * Creates a new MonoLogManager instance.
     * @param string $directory
     * @param array $init
     * @param ?string $name
     */
    public function __construct( string $directory = Char::EMPTY , array $init = [] , ?string $name = null )
    {
        parent::__construct( $directory , $init , $name ) ;
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
     * Indicates if the bubbling is active.
     * @var bool
     */
    public bool $bubbles = true ;

    /**
     * The date format of the log files.
     * @var string
     */
    public string $dateFormat = 'Y-m-d H:i:s' ;

    /**
     * The file permission.
     * @var int|float
     */
    public int|float $filePermissions = 0664 ;

    /**
     * The format of the log messages.
     * @var string
     */
    public string $format = "%datetime% %channel% %level_name% %message% %context% %extra%\n" ;

    /**
     * Include stack traces in exception logs.
     * @var bool
     */
    public bool $includeStackTraces = false ;

    /**
     * Whether to ignore empty context and extra.
     * @var bool
     */
    public bool $ignoreEmptyContextAndExtra = true ;

    /**
     * The default level of the logger.
     * @var int|Level
     */
    public int|Level $level = Level::Debug ;

    /**
     * The maximum number of files stored in the log folder.
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
        $this->ensureDirectory();

        $logger = new Logger( $this->getFileName() );

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
     * The line formatter.
     * @var ?FormatterInterface
     */
    protected ?FormatterInterface $formatter = null ;
}