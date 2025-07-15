<?php

namespace oihana\logging;

use Psr\Log\LoggerInterface;

use oihana\enums\Char;

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
        return implode( DIRECTORY_SEPARATOR , [ $this->directory , $this->path ]  );
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
     * @return string
     */
    public function getFilePath():string
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getFileName() . $this->getExtension() ;
    }

    /**
     * Returns the list of lines of a specific log file.
     * @param string $file
     * @return ?array
     */
    public function getLogLines( string $file ) : ?array
    {
        $file = $this->getDirectory() . DIRECTORY_SEPARATOR . $file ;
        if( file_exists( $file ) )
        {
            $lines = [] ;
            clearstatcache();
            if( filesize( $file ) > 0 )
            {
                $file = fopen( $file , self::READ ) ;

                while ( !feof( $file ) )
                {
                    $lines[] = fgets( $file );
                }

                if( count($lines) > 0 )
                {
                    array_pop( $lines ) ;
                    $lines = array_map( fn( $line ) :?array => $this->createLog( $line ) , $lines ) ;
                }

                fclose( $file ) ;
            }
            return $lines ;
        }
        return null ;
    }

    public function getLoggerFiles() :array|false
    {
        $directory = $this->getDirectory() ;
        $files= scandir( $directory ) ;
        if( is_array($files) && count( $files ) > 0 && isset( $this->extension ) )
        {
            return array_values( array_filter( $files , fn( $file ) => str_starts_with( $file , $this->name ?? Char::EMPTY ) && str_ends_with( $file ?? Char::EMPTY , $this->extension ) ) ) ;
        }
        return [] ;
    }
}