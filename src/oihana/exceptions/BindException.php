<?php

namespace oihana\exceptions;

use Exception;

/**
 * Occurs when a bind variable is not valid or failed.
 *
 * @package oihana\exceptions
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class BindException extends Exception
{
    use ExceptionTrait ;
}