<?php

namespace oihana\exceptions ;

use Exception ;

/**
 * An exception thrown when a http response failed.
 *
 * @package oihana\exceptions
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class ResponseException extends Exception
{
    use ExceptionTrait ;
}