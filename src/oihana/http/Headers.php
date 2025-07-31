<?php

namespace oihana\http;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Defines the HTTP headers.
 */
class Headers
{
    use ConstantsTrait ;

    const string ACCESS_CONTROL_ALLOW_CREDENTIALS = 'Access-Control-Allow-Credentials' ;
    const string ACCESS_CONTROL_ALLOW_HEADERS     = 'Access-Control-Allow-Headers' ;
    const string ACCESS_CONTROL_ALLOW_METHODS     = 'Access-Control-Allow-Methods' ;
    const string ACCESS_CONTROL_ALLOW_ORIGIN      = 'Access-Control-Allow-Origin'  ;
    const string ACCESS_CONTROL_MAX_AGE           = 'Access-Control-Max-Age'       ;
    const string ACCEPT                           = 'Accept' ;
    const string CACHE_CONTROL                    = 'Cache-Control' ;
    const string CONTENT_DISPOSITION              = 'Content-Disposition' ;
    const string CONTENT_LENGTH                   = 'Content-Length' ;
    const string CONTENT_TYPE                     = 'Content-Type' ;
    const string ETAG                             = 'ETag' ;
    const string EXPIRES                          = 'Expires' ;
    const string LAST_MODIFIED                    = 'Last-Modified' ;
    const string LOCATION                         = 'Location' ;
    const string PRAGMA                           = 'Pragma' ;
    const string VARY                             = 'Vary' ;
}