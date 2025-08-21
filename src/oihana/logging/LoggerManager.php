<?php

namespace oihana\logging;

use oihana\enums\Order;
use oihana\files\enums\FindFilesOption;
use oihana\files\enums\FindMode;
use oihana\files\exceptions\DirectoryException;
use oihana\files\exceptions\FileException;
use Psr\Log\LoggerInterface;

use oihana\enums\Char;
use function oihana\files\findFiles;
use function oihana\files\getFileLines;
use function oihana\files\path\joinPaths;

/**
 * A logger manager.
 */
abstract class LoggerManager
{
    /**
     * Creates a new MonoLogManager instance.
     * @param string $directory
     * @param array $init
     * @param ?string $name
     */
    public function __construct( string $directory = Char::EMPTY , array $init = [] , ?string $name = null )
    {
        $this->name      = $name  ;
        $this->directory = $directory ;
        $this->path      = $init[ LoggerConfig::PATH ] ?? self::DEFAULT_PATH ;
        $this->extension = $init[ LoggerConfig::EXTENSION ] ?? self::DEFAULT_EXTENSION ;
    }

    public const string DEFAULT_NAME = 'log' ;
    public const string DEFAULT_EXTENSION = '.log' ;
    public const string DEFAULT_PATH = 'log' ;

    public const string READ  = 'r' ;
    public const string WRITE = 'w' ;

    /**
     * The log directory.
     * @var string|mixed
     */
    public string $directory = Char::EMPTY ;

    /**
     * The extension of the logs files.
     * @var string|mixed
     */
    public string $extension ;

    /**
     * The name of the logging channel, a simple descriptive name that is attached to all log records.
     * @var string|null
     */
    public ?string $name ;

    /**
     * The path of the log folder.
     * @var string
     */
    public string $path ;

    /**
     * Clear the content of the specific file.
     * @param string $file
     * @return bool
     */
    public function clear( string $file ):bool
    {
        $path = $this->getDirectory() . DIRECTORY_SEPARATOR . $file ;
        if( file_exists( $path ) )
        {
            $file = fopen( $path , self::WRITE ) ;
            ftruncate( $file, 0 );
            fclose( $file );
            if ( filesize( $path ) === 0 )
            {
                return true ;
            }
        }
        return false ;
    }

    /**
     * Returns the number of lines in a file.
     * @param string $file
     * return int
     */
    public function countLines( string $file ) :int
    {
        $count = 0 ;
        $file = $this->getDirectory() . DIRECTORY_SEPARATOR . $file ;
        if( file_exists( $file ) )
        {
            $stream = fopen( $file, self::READ ) ;
            while( !feof( $stream ) )
            {
                $line = fgets( $stream ) ;
                if( $line !== false )
                {
                    $count ++;
                }
            }
            fclose( $stream ) ;
        }
        return $count ;
    }

    /**
     * Create a basic log definition.
     * @param string $line
     * @return array|null
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

    abstract public function createLogger():LoggerInterface ;

    /**
     * Returns the log directory.
     * @return string
     */
    public function getDirectory() :string
    {
        return joinPaths( $this->directory , $this->path ) ;
    }

    /**
     * Returns the extension of the log files.
     * @return string
     */
    public function getExtension():string
    {
        return $this->extension ?? self::DEFAULT_EXTENSION ;
    }

    /**
     * Returns the log file name.
     * @return string
     */
    public function getFileName():string
    {
        return $this->name ?? self::DEFAULT_NAME ;
    }

    /**
     * Returns the full log path.
     * @param ?string $file The optional file name.
     * @return string
     */
    public function getFilePath( ?string $file = null ):string
    {
        return joinPaths( $this->getDirectory() , $file ?? ( $this->getFileName() . $this->getExtension() ) ) ;
    }

    /**
     * Returns the list of lines of a specific log file.
     * @param string $file
     * @return ?array
     * @throws FileException
     */
    public function getLogLines( string $file ) : ?array
    {
        $file = $this->getFilePath( $file ) ;
        return file_exists( $file ) ? getFileLines( $file ) : null ;
    }

    /**
     * Return the logger files.
     * @throws DirectoryException
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