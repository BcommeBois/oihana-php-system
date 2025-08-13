<?php

namespace oihana\http;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Defines the strategy to retrieves parameters in http request (body, query or both).
 */
class HttpParamStrategy
{
    use ConstantsTrait ;

    public const string BODY  = 'body' ;
    public const string BOTH  = 'both' ;
    public const string QUERY = 'query' ;
}