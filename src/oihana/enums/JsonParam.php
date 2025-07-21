<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Enumeration of common parameter names used in JSON encoding and decoding operations.
 *
 * This class defines string constants representing typical options passed to JSON
 * functions such as `json_encode()` and `json_decode()` in PHP.
 *
 * These keys correspond to:
 * - `associative`: Determines if JSON objects should be decoded as associative arrays.
 * - `depth`: Sets the maximum recursion depth for encoding or decoding.
 * - `flags`: Defines bitmask flags to modify the behavior of JSON operations.
 *
 * Example usage:
 * ```php
 * use oihana\enums\JsonParam;
 *
 * $option = [
 *     JsonParam::ASSOCIATIVE => true,
 *     JsonParam::DEPTH       => 512,
 *     JsonParam::FLAGS       => JSON_PRETTY_PRINT,
 * ];
 * ```
 *
 * @package oihana\enums
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class JsonParam
{
    use ConstantsTrait ;

    public const string ASSOCIATIVE = 'associative' ;
    public const string DEPTH       = 'depth' ;
    public const string FLAGS       = 'flags' ;

    /**
     * Returns default values for JSON parameters.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultValues(): array
    {
        return
        [
            self::ASSOCIATIVE => false,
            self::DEPTH       => 512,
            self::FLAGS       => 0,
        ];
    }

    /**
     * Checks if the given flags value is valid JSON flag or combination of flags.
     * You may add more flags as needed.
     *
     * @param int $flags
     * @return bool
     */
    public static function isValidFlags( int $flags ): bool
    {
        $validFlags = JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
            | JSON_FORCE_OBJECT
            | JSON_NUMERIC_CHECK
            | JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_PARTIAL_OUTPUT_ON_ERROR
            | JSON_PRESERVE_ZERO_FRACTION
            | JSON_THROW_ON_ERROR;

        return ( $flags & ~$validFlags ) === 0 ;
    }
}