<?php

namespace oihana\init;

use oihana\enums\IniOptions;
use oihana\reflections\exceptions\ConstantException;

/**
 * Initialize the global PHP error settings.
 *
 * This function sets PHP error reporting and ini directives related to error display
 * and logging, based on the provided configuration array and optional log root path.
 *
 * It applies the following settings:
 * - error_reporting level (defaulting to $defaultErrorLevel if not provided)
 * - display_errors (ini directive)
 * - display_startup_errors (ini directive)
 * - error_log (ini directive), with an optional root path prefix
 *
 * @param array|null $init Optional associative array of ini settings.
 * @param string|null $logRootPath Optional root directory path to prepend to error_log path if it is relative.
 * @param int $defaultErrorLevel Default error reporting level to use if none is set in $init.
 *
 * @return void
 *
 * @throws ConstantException
 *
 * @see IniOptions
 */
function initErrors( ?array $init = null , ?string $logRootPath = null , int $defaultErrorLevel = E_ALL ) : void
{
    $init = $init ?? [] ;

    error_reporting(  $init[ IniOptions::ERROR_REPORTING ] ?? $defaultErrorLevel ) ;

    setIniIfExists( IniOptions::DISPLAY_ERRORS         , $init ) ;
    setIniIfExists( IniOptions::DISPLAY_STARTUP_ERRORS , $init ) ;

    $error_log = $init[ IniOptions::ERROR_LOG ] ?? null ;
    if( isset( $error_log ) && trim( $error_log ) !== '' )
    {
        if( isset( $logRootPath ) && trim( $logRootPath ) !== '' )
        {
            $logRootPath = rtrim( $logRootPath , DIRECTORY_SEPARATOR ) ;
            $error_log   = $logRootPath . DIRECTORY_SEPARATOR . ltrim( $error_log , DIRECTORY_SEPARATOR ) ;
        }
        setIniIfExists(IniOptions::ERROR_LOG, $error_log);
    }
}
