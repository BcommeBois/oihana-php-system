<?php

namespace oihana\exceptions ;

use Exception ;

/**
 * An exception thrown when a validation failed.
 */
class ResponseException extends Exception
{
    use ExceptionTrait ;
}