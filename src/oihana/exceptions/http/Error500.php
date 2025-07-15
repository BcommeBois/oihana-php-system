<?php

namespace oihana\exceptions\http ;

use Exception;
use Throwable;
use oihana\exceptions\ExceptionTrait;

/**
 * Throw an 'internal server error' (500).
 */
class Error500 extends Exception
{
    /**
     * Creates a new Error500 instance.
     * @param string $message
     * @param int $code
     * @param Throwable|null $notFound
     */
    public function __construct( string $message = '' , int $code = 500 , Throwable|null $notFound = null )
    {
        parent::__construct( $message , $code , $notFound ) ;
    }

    use ExceptionTrait ;
}