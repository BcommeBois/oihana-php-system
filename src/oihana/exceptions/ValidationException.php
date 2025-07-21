<?php

namespace oihana\exceptions ;

use Exception ;
use oihana\enums\Char;

/**
 * An exception thrown when a validation failed.
 *
 * @package oihana\exceptions
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class ValidationException extends Exception
{
    /**
     * Returns a string representation of the exception
     * @return string The string representation of the object.
     * @magic
     */
    public function __toString() :string
    {
        return Char::LEFT_BRACKET . __CLASS__ . ' message:' . $this->getMessage() . Char::RIGHT_BRACKET ;
    }
}