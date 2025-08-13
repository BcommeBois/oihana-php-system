<?php

namespace oihana\http;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Defines constants for common HTTP request methods.
 */
class HttpMethod
{
    use ConstantsTrait ;

    public const string DELETE = 'DELETE';
    public const string delete = 'delete';

    public const string HEAD = 'HEAD';
    public const string head = 'head';

    public const string GET = 'GET';
    public const string get = 'get';

    public const string OPTIONS = 'OPTIONS';
    public const string options = 'options';

    public const string PATCH = 'PATCH';
    public const string patch = 'patch';

    public const string POST = 'POST';
    public const string post = 'post';

    public const string PURGE = 'PURGE';
    public const string purge = 'purge';

    public const string PUT = 'PUT';
    public const string put = 'put';

    public const string TRACE = 'TRACE';
    public const string trace = 'trace';

    // ------ extras

    public const string ALL      = 'ALL' ;
    public const string CONNECT  = 'CONNECT';

    public const string all       = 'all' ;
    public const string connect   = 'connect';
    public const string default   = 'default' ;
    public const string deleteAll = 'deleteAll' ;
    public const string count     = 'count' ;
    public const string exist     = 'exist' ;
    public const string flush     = 'flush' ;
    public const string insert    = 'insert' ;
    public const string list      = 'list' ;
    public const string replace   = 'replace' ;
    public const string truncate  = 'truncate' ;
    public const string update    = 'update' ;
    public const string upsert    = 'upsert' ;
}