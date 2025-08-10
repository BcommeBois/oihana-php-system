<?php

namespace oihana\init;

use oihana\enums\IniOptions;
use oihana\reflections\exceptions\ConstantException;

/**
 * Set a PHP ini directive from a scalar or from a config array if the value exists and is not empty.
 *
 * Behavior:
 * - If $init is an array, this function looks up $init[$key]; otherwise it treats $init as the value.
 * - The value is cast to string and trimmed; empty strings are ignored.
 * - ini_set() is invoked only if the function exists and a non-empty value is provided.
 * - Returns true when ini_set() is called, false otherwise.
 *
 * Notes:
 * - Most ini values are strings; booleans are commonly expressed as "1"/"0". Passing true/false will be cast to "1"/"" by PHP.
 * - Use ini_get($key) to read back the effective value after setting.
 *
 * @param string $key The ini key (e.g. 'display_errors', 'memory_limit').
 * @param array|string|int|float|bool|null $init A config array (looked up by $key) or a direct scalar value.
 * @return bool True if ini_set() was called, false otherwise.
 *
 * @throws ConstantException
 * @see ini_get()
 * @link https://www.php.net/manual/en/function.ini-set.php PHP manual: ini_set
 *
 * @example
 * ```php
 * use function oihana\init\setIniIfExists;
 *
 * // From a configuration array
 * $config =
 * [
 *     'display_errors' => '1',
 *     'memory_limit'   => '256M',
 * ];
 * setIniIfExists('display_errors', $config); // calls ini_set('display_errors', '1')
 * setIniIfExists('upload_max_filesize', $config); // returns false (not present)
 *
 * // From a direct scalar
 * setIniIfExists('memory_limit', '512M'); // calls ini_set('memory_limit', '512M')
 * setIniIfExists('display_errors', "\t\n"); // returns false (empty after trim)
 * ```
 * @see ini_set()
 */
function setIniIfExists ( string $key , array|string|int|float|bool|null $init = [] ) : bool
{
    IniOptions::validate( $key ) ;
    $value = is_array( $init ) ? ( $init[ $key ] ?? null) : $init;
    if ( function_exists('ini_set') && isset( $value ) && trim( (string) $value ) !== '' )
    {
        ini_set( $key , $value ) ;
        return true ;
    }
    return false ;
}
