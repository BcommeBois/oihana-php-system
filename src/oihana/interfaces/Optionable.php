<?php

namespace oihana\interfaces;

use oihana\enums\Char;

/**
 * This interface is implemented by the optionable enumerations.
 *
 * @package oihana\interfaces
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
interface Optionable
{
    /**
     * Returns the option value with the specific option property name.
     * @param string $name The name of the option constant.
     * @param string $prefix The optional prefix to append before the option name.
     * @return string|null
     */
    public static function getOption( string $name , string $prefix = Char::EMPTY ): ?string ;
}