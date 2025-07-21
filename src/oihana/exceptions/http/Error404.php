<?php

namespace oihana\exceptions\http ;

use Exception;
use Throwable;
use oihana\exceptions\ExceptionTrait;

/**
 * Throw an 'not found error' (404).
 * @package oihana\exceptions\http
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class Error404 extends Exception
{
    /**
     * Creates a new Error404 instance.
     * @param string $message
     * @param int $code
     * @param Throwable|null $notFound
     */
    public function __construct( string $message = '' , int $code = 404 , Throwable|null $notFound = null )
    {
        parent::__construct( $message , $code , $notFound ) ;
    }

    use ExceptionTrait ;
}