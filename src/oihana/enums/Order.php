<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Enumeration of sorting direction values, often used in SQL, data queries, or collection sorting.
 *
 * This class provides both lower-case and upper-case variants of common sorting keywords:
 * - `asc`, `ASC`: Ascending order
 * - `desc`, `DESC`: Descending order
 *
 * Example usage:
 * ```php
 * use oihana\enums\Order;
 *
 * $query = 'SELECT * FROM users ORDER BY name ' . Order::ASC;
 * ```
 *
 * You may want to normalize user input or validate sorting directions using helper methods.
 *
 * @package oihana\enums
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class Order
{
    use ConstantsTrait ;

    /**
     * The ascending order (lower case).
     */
    public const string asc  = 'asc' ;

    /**
     * The ascending order (upper case).
     */
    public const string ASC  = 'ASC' ;

    /**
     * The descending order (lower case).
     */
    public const string desc = 'desc' ;

    /**
     * The descending order (upper case).
     */
    public const string DESC = 'DESC' ;

    /**
     * Returns the canonical order keyword ("ASC" or "DESC") from a case-insensitive input.
     *
     * @param string $value The order string.
     * @return string|null  The normalized value (e.g., "ASC", "DESC") or null if invalid.
     */
    public static function normalize( string $value ): ?string
    {
        $value = strtolower( trim( $value ) ) ;
        return match ( $value )
        {
            'asc'  => self::ASC  ,
            'desc' => self::DESC ,
            default => null,
        };
    }
}
