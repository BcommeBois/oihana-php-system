<?php

namespace oihana\logging\enums;

use oihana\reflect\traits\ConstantsTrait;

class LoggerParam
{
    use ConstantsTrait ;

    public const string DIRECTORY = 'directory' ;
    public const string EXTENSION = 'extension' ;
    public const string NAME      = 'name' ;
    public const string PATH      = 'path' ;
}