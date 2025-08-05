<?php

namespace oihana\interfaces;

/**
 * Interface for objects that can be converted to an array,
 * with an option to remove empty or null values.
 *
 * @package oihana\interfaces
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
interface ClearableArrayable
{
    /**
     * Converts the object to an associative array.
     *
     * If $clear is true, removes entries with null or empty values.
     *
     * @param bool $clear Whether to remove null or empty values from the array. Default: false.
     *
     * @return array The array representation of the object.
     */
    public function toArray( bool $clear = false ): array ;
}