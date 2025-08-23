<?php

namespace oihana\logging;

use Monolog\ErrorHandler;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;

use Psr\Log\LoggerInterface;

use oihana\enums\Char;
use oihana\logging\enums\MonoLogParam;

/**
 * A logger manager.
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
        $this->bubbles         = boolval( $init[ MonoLogParam::BUBBLES  ] ?? $this->bubbles ) ;
        $this->dirPermissions  = octdec( $init[ MonoLogParam::DIR_PERMISSIONS ] ?? '0775' ) ;
        $this->filePermissions = octdec( $init[ MonoLogParam::FILE_PERMISSIONS ] ?? '0664' ) ;
        $this->level           = intval( $init[ MonoLogParam::LEVEL ] ?? Level::Debug ) ;
        $this->maxFiles        = intval( $init[ MonoLogParam::MAX_FILES ] ?? $this->maxFiles ) ;
    }

    /**
     * Indicates if the bubbling is active.
     * @var bool
     */
    public bool $bubbles = true ;

    /**
     * The directory permission.
     * @var int|float
     */
    public int|float $dirPermissions = 0775 ;

    /**
     * The file permission.
     * @var int|float
     */
    public int|float $filePermissions = 0664 ;

    /**
     * The line formatter.
     * @var ?FormatterInterface
     */
    protected ?FormatterInterface $formatter = null ;

    /**
     * The default level of the logger.
     * @var int|Level
     */
    public int|Level $level ;

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
     */
    public function createLogger():LoggerInterface
    {
        $directory = $this->directory ;

        if ( !file_exists( $directory ) )
        {
            mkdir( dirname( $directory ) , $this->dirPermissions , true ) ;
        }

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
            $this->setFormatter() ;
        }
        return $this->formatter ;
    }

    /**
     * Sets the formatter for log entries.
     * @return void
     */
    public function setFormatter():void
    {
        $this->formatter = new LineFormatter
        (
            "%datetime% %channel% %level_name% %message% %context% %extra%\n",
            "Y-m-d H:i:s",
            true,
            true ,
            false
        );
    }
}