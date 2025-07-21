<?php

namespace oihana\exceptions;

use Exception;

/**
 * An exception thrown when an operation is unsupported.
 *
 * @package oihana\exceptions
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class UnsupportedOperationException extends Exception
{
    use ExceptionTrait ;
}