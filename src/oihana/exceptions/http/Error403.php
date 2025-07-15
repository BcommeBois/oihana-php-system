<?php

namespace oihana\exceptions\http ;

use Exception;
use Throwable;
use oihana\exceptions\ExceptionTrait;

/**
 * Throw an 'Forbidden error' (403).
 */
class Error403 extends Exception
{
    /**
     * Creates a new Error403 instance.
     * @param string $message
     * @param int $code
     * @param Throwable|null $notFound
     */
    public function __construct( string $message = '' , int $code = 403 , Throwable|null $notFound = null )
    {
        parent::__construct( $message , $code , $notFound ) ;
    }

    use ExceptionTrait ;
}