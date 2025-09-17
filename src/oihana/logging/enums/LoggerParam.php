<?php

namespace oihana\logging\enums;

use oihana\reflect\traits\ConstantsTrait;

class LoggerParam
{
    use ConstantsTrait ;

    public const string DIRECTORY       = 'directory' ;
    public const string DIR_PERMISSIONS = 'dirPermissions' ;
    public const string EXTENSION       = 'extension' ;
    public const string LOGGABLE        = 'loggable' ;
    public const string LOGGER          = 'logger' ;
    public const string NAME            = 'name' ;
    public const string PATH            = 'path' ;
}