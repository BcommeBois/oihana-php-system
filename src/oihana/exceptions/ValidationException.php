<?php

namespace oihana\exceptions ;

use Exception ;
use oihana\enums\Char;

/**
 * An exception thrown when a validation failed.
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