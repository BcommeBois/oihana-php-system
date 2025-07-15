<?php

namespace oihana\exceptions;

use Exception;

/**
 * An exception thrown when an operation is unsupported.
 */
class UnsupportedOperationException extends Exception
{
    use ExceptionTrait ;
}