<?php

namespace oihana\logging;

use oihana\enums\Order;
use oihana\files\enums\FindFilesOption;
use oihana\files\enums\FindMode;
use oihana\files\exceptions\DirectoryException;
use oihana\files\exceptions\FileException;

use Psr\Log\LoggerInterface;

use oihana\enums\Char;

use function oihana\files\clearFile;
use function oihana\files\countFileLines;
use function oihana\files\findFiles;
use function oihana\files\getFileLines;
use function oihana\files\path\joinPaths;

/**
 * Abstract logger manager class.
 *
 * Provides utility methods for managing log files, including:
 * - Creating a logger instance.
 * - Reading log lines.
 * - Counting log lines.
 * - Clearing log files.
 * - Retrieving log file paths and names.
 *
 * This class is meant to be extended with a concrete implementation of `createLogger()`.
 */
abstract class LoggerManager
{
    /**
     * Constructor.
     *
     * @param string $directory Base directory for log storage.
     * @param array $init Optional initialization options:
     *                    - LoggerConfig::PATH: subdirectory path
     *                    - LoggerConfig::EXTENSION: log file extension
     * @param string|null $name Optional logger channel name.
     */
    public function __construct( string $directory = Char::EMPTY , array $init = [] , ?string $name = null )
    {
        $this->name      = $name  ;
        $this->directory = $directory ;
        $this->path      = $init[ LoggerConfig::PATH ] ?? self::DEFAULT_PATH ;
        $this->extension = $init[ LoggerConfig::EXTENSION ] ?? self::DEFAULT_EXTENSION ;
    }

    /**
     * Default log file name.
     */
    public const string DEFAULT_NAME = 'log';

    /**
     * Default log file extension.
     */
    public const string DEFAULT_EXTENSION = '.log';

    /**
     * Default log folder path (relative to $directory).
     */
    public const string DEFAULT_PATH = 'log';

    /**
     * The base directory for log storage.
     *
     * @var string
     */
    public string $directory = Char::EMPTY ;

    /**
     * The file extension used for log files (e.g., ".log").
     * @var string
     */
    public string $extension ;

    /**
     * Optional name of the logging channel.
     * Used as prefix for log files and as a descriptive label.
     *
     * @var string|null
     */
    public ?string $name ;

    /**
     * Subfolder or path where log files are stored (relative to $directory).
     *
     * @var string
     */
    public string $path ;

    /**
     * Clears the content of a specific log file.
     *
     * @param string $file Log file name.
     *
     * @return bool True if the file was cleared, false if file does not exist.
     *
     * @throws FileException If clearing fails due to filesystem permissions or errors.
     */
    public function clear( string $file ):bool
    {
        $file = $this->getFilePath( $file ) ;
        if( file_exists( $file ) )
        {
            return clearFile( $file , false )  ;
        }
        return false ;
    }

    /**
     * Returns the number of lines in a log file.
     *
     * @param string $file Log file name.
     *
     * @return int Number of lines in the file.
     *
     * @throws FileException If file cannot be read.
     */
    public function countLines( string $file ) :int
    {
        return countFileLines( $file ) ;
    }

    /**
     * Parses a log line into a structured array.
     *
     * Example: "2025-08-21 10:30:00 INFO Some message" becomes:
     * [
     *     'date' => '2025-08-21',
     *     'time' => '10:30:00',
     *     'level' => 'INFO',
     *     'message' => 'Some message'
     * ]
     *
     * @param string $line Raw log line.
     * @return array|null Parsed log entry or null if line is empty or malformed.
     */
    public function createLog( string $line ) :?array
    {
        if( $line != Char::EMPTY )
        {
            $line = explode(Char::SPACE , $line ) ;
            if( count($line) > 3 )
            {
                [ $date , $time , $level ] = $line ;
                $line    = array_slice( $line , 3 ) ;
                $message = implode(Char::SPACE , $line) ;
                return [ "date" => $date , "time" => $time , "level" => $level , 'message' => $message ] ;
            }
        }
        return null ;
    }

    /**
     * Must be implemented by subclasses to return a PSR-3 compliant logger.
     *
     * @return LoggerInterface
     */
    abstract public function createLogger():LoggerInterface ;

    /**
     * Returns the full path of the log directory.
     *
     * @return string Absolute path to the log folder.
     */
    public function getDirectory() :string
    {
        return joinPaths( $this->directory , $this->path ) ;
    }

    /**
     * Returns the file extension used for log files.
     *
     * @return string Log file extension, including dot (e.g., ".log").
     */
    public function getExtension():string
    {
        return $this->extension ?? self::DEFAULT_EXTENSION ;
    }

    /**
     * Returns the base name of the log file.
     *
     * @return string Log file name.
     */
    public function getFileName():string
    {
        return $this->name ?? self::DEFAULT_NAME ;
    }

    /**
     * Returns the full path to a log file.
     *
     * @param string|null $file Optional custom file name. Defaults to <name><extension>.
     * @return string Full path to the log file.
     */
    public function getFilePath( ?string $file = null ):string
    {
        return joinPaths( $this->getDirectory() , $file ?? ( $this->getFileName() . $this->getExtension() ) ) ;
    }

    /**
     * Returns the lines of a log file as an array of structured entries.
     *
     * @param ?string $file Log file name.
     * @return array|null Array of parsed log entries or null if file does not exist.
     * @throws FileException If the file cannot be read.
     */
    public function getLogLines( ?string $file ) : ?array
    {
        $file = $this->getFilePath( $file ) ;
        return file_exists( $file ) ? getFileLines( $file , [ $this , 'createLog' ] ) : null ;
    }


    /**
     * Returns the list of log files in the log directory matching the current logger name and extension.
     *
     * @return array|false List of log file names or false on error.
     * @throws DirectoryException If the log directory cannot be read.
     */
    public function getLoggerFiles() :array|false
    {
        $directory = $this->getDirectory() ;
        $files = findFiles($directory,
        [
            FindFilesOption::PATTERN => $this->name . Char::ASTERISK . $this->extension,
            FindFilesOption::MODE    => FindMode::FILES ,
            FindFilesOption::ORDER   => Order::asc ,
            FindFilesOption::SORT    => fn( $a , $b ) => strcmp( $a->getFilename() , $b->getFilename() ) ,
        ]);
        return array_map( fn($file) => $file->getFilename() , $files ) ;
    }
}