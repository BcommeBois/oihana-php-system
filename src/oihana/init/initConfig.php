<?php

namespace oihana\init;

use Devium\Toml\TomlError;

use oihana\enums\Char;
use oihana\files\exceptions\DirectoryException;
use oihana\files\exceptions\FileException;

use function oihana\files\toml\resolveTomlConfig;

/**
 * Load a toml configuration and initialize it.
 *
 * Delegates resolution to oihana\files\toml\resolveTomlConfig while preserving
 * the previous behavior of returning an empty config when the file does not exist.
 *
 * @param string                 $basePath   Base path of config
 * @param string                 $file       Config file name (with or without .toml)
 * @param ?callable(array):array $init       Initialize the application with the config definition.
 *
 * @return array The config array definition.
 *
 * @throws TomlError
 */
function initConfig( string $basePath = Char::EMPTY , string $file = 'config.toml' , ?callable $init = null ) : array
{
    try
    {
        $defaultPath = ( $basePath !== Char::EMPTY ) ? $basePath : null ;
        $config      = resolveTomlConfig( $file , [] , $defaultPath ) ;
    }
    catch ( FileException | DirectoryException $e )
    {
        $config = [] ;
    }
    return isset( $init ) ? $init( $config ) : $config ;
}
