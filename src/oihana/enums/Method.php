<?php

namespace oihana\enums;

use oihana\http\HttpMethod;

/**
 * Defines constants for common methods.
 *
 * @see HttpMethod
 */
class Method extends HttpMethod
{
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