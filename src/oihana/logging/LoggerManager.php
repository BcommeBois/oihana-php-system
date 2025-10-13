<?php

namespace oihana\logging;

use ReflectionException;

use Psr\Log\LoggerInterface;

use oihana\enums\Char;
use oihana\enums\Order;
use oihana\files\enums\FindFilesOption;
use oihana\files\enums\FindMode;
use oihana\files\exceptions\DirectoryException;
use oihana\files\exceptions\FileException;
use oihana\logging\enums\LoggerParam;

use fr\ooop\schema\Log;

use function oihana\files\clearFile;
use function oihana\files\countFileLines;
use function oihana\files\findFiles;
use function oihana\files\getFileLines;
use function oihana\files\path\joinPaths;

/**
 * Abstract base class for managing log files and PSR-3 loggers.
 *
 * LoggerManager provides utility methods to handle log files and directories,
 * including creating, reading, counting lines, clearing logs, and retrieving
 * log paths and names.
 *
 * Features:
 * - Abstract `createLogger()` method to return a PSR-3 compliant logger.
 * - Automatic creation of log directories if they do not exist.
 * - Directory creation uses a temporary `umask 0002` to ensure group writable
 *   permissions (e.g., 0664 for files, 2775 for directories) in collaborative
 *   environments.
 * - Utilities to read log lines as structured entries (`createLog()`).
 * - Counting lines (`countLines()`) and clearing files (`clear()`).
 * - Listing log files (`getLoggerFiles()`), filtered by name and extension.
 * - Flexible configuration via constructor array:
 *   - `directory`: Base log directory path.
 *   - `dirPermissions`: Permissions for new directories.
 *   - `path`: Subfolder path within the base directory.
 *   - `extension`: Log file extension.
 *   - `name`: Optional logger channel name.
 *
 * Example usage:
 * ```php
 * $loggerManager = new class(['directory' => '/var/log/myapp']) extends LoggerManager
 * {
 *     public function createLogger(): LoggerInterface
 *     {
 *         // return a PSR-3 logger instance
 *     }
 * };
 *
 * $loggerManager->ensureDirectory();
 * $lines = $loggerManager->getLogLines(null); // read lines from default log file
 * ```
 *
 * @package oihana\logging
 * @author  Marc Alcaraz
 * @since   1.0.0
 */
abstract class LoggerManager
{
    /**
     * Creates a new LoggerManager instance.
     *
     * @param array{
     *     directory?      : string|null , // The log directory path
     *     dirPermissions? : int|null    , // The log directory permission
     *     extension?      : string|null , // The log file extension
     *     path?           : string|null , // The subdirectory path
     *
     * } $init Optional initialization options
     *
     * @param string|null $name Optional logger channel name.
     */
    public function __construct( array $init = [] , ?string $name = null )
    {
        $this->directory = $init[ LoggerParam::DIRECTORY ] ?? $this->directory ;
        $this->extension = $init[ LoggerParam::EXTENSION ] ?? $this->extension ;
        $this->name      = $name  ;
        $this->path      = $init[ LoggerParam::PATH ] ?? $this->path ;

        $dirPermissions = $init[ LoggerParam::DIR_PERMISSIONS ] ?? $this->dirPermissions;
        $this->dirPermissions = is_string( $dirPermissions ) ? octdec( $dirPermissions ) : (int)$dirPermissions ;
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
     * Default permissions for newly created directories.
     * Group writable (g+w) for collaborative environments.
     */
    public string $directory = Char::EMPTY ;

    /**
     * The directory permission.
     * @var int|float
     */
    public int|float $dirPermissions = 0775 ;

    /**
     * The file extension used for log files (e.g., ".log").
     * @var string
     */
    public string $extension = '.log' ;

    /**
     * Optional name of the logging channel.
     * Used as prefix for log files and as a descriptive label.
     *
     * @var string|null
     */
    public ?string $name = null ;

    /**
     * Subfolder or path where log files are stored (relative to $directory).
     * @var string
     */
    public string $path = Char::EMPTY ;

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
        return countFileLines( $this->getFilePath( $file ) ) ;
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
     * @return Log|null Parsed log entry or null if line is empty or malformed.
     * @throws ReflectionException
     */
    public function createLog( string $line ) :?Log
    {
        if( $line != Char::EMPTY )
        {
            $line = explode(Char::SPACE , $line ) ;
            if( count($line) > 3 )
            {
                [ $date , $time , $level ] = $line ;
                $line    = array_slice( $line , 3 ) ;
                $message = implode(Char::SPACE , $line) ;
                return new Log
                ([
                    Log::DATE    => $date   ,
                    Log::TIME    => $time   ,
                    Log::LEVEL   => $level  ,
                    Log::MESSAGE => $message
                ]) ;
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
     * Ensure the log directory exists and is writable.
     *
     * If the directory does not exist, it will be created recursively.
     * A temporary `umask 0002` is applied so that the directory is group writable
     * according to `$this->dirPermissions` (default 2775). Files created within
     * the directory will inherit group write permission (0664 by default).
     *
     * Throws a DirectoryException if the directory cannot be created or is not writable.
     *
     * @throws DirectoryException if the directory cannot be created or is not writable.
     */
    public function ensureDirectory(): void
    {
        $dir = $this->getDirectory();

        if ( !is_dir( $dir ) )
        {
            if ( !@mkdir($dir, $this->dirPermissions, true) && !is_dir( $dir ) )
            {
                throw new DirectoryException("Unable to create the log directory: $dir");
            }
        }

        if ( !is_writable( $dir ) )
        {
            throw new DirectoryException("Log directory is not writable: $dir" ) ;
        }
    }

    /**
     * Returns the full path of the log directory.
     *
     * Combines the base `$this->directory` and `$this->path` using canonical path
     * normalization. The returned path is absolute (or relative if `$this->directory`
     * is relative) and can be safely used for file operations.
     *
     * @return string Absolute or relative path to the log folder.
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
     * If `$file` is null, returns the default log file path: <directory>/<name><extension>.
     * Uses `joinPaths()` to safely concatenate directory and file name.
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
     * The search uses `findFiles()` with options:
     * - Pattern: "<logger name>*<extension>"
     * - Mode: files only
     * - Order: ascending by file name
     *
     * Throws a DirectoryException if the log directory cannot be read.
     *
     * @return array|false List of log file names or false on error.
     * @throws DirectoryException if the log directory cannot be read.
     */
    public function getLoggerFiles() :array|false
    {
        $files = findFiles( $this->getDirectory() ,
        [
            FindFilesOption::PATTERN => $this->name . Char::ASTERISK . $this->extension,
            FindFilesOption::MODE    => FindMode::FILES ,
            FindFilesOption::ORDER   => Order::asc ,
            FindFilesOption::SORT    => fn( $a , $b ) => strcmp( $a->getFilename() , $b->getFilename() ) ,
        ]);

        return array_map( fn($file) => $file->getFilename() , $files ) ;
    }
}