<?php

namespace oihana\exceptions ;

use oihana\enums\Char;

/**
 * The exception trait helper.
 *
 * @package oihana\exceptions
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait ExceptionTrait
{
    /**
     * Returns a string representation of the exception
     * @return string The string representation of the object.
     * @magic
     */
    public function __toString() :string
    {
        return Char::LEFT_BRACKET . __CLASS__ . ' code:' . $this->getCode() . ' message:' . $this->getMessage() . Char::RIGHT_BRACKET ;
    }
}