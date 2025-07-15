<?php

namespace oihana\logging;

use oihana\reflections\traits\ConstantsTrait;

class LoggerConfig
{
    use ConstantsTrait ;

    public const string DIRECTORY = 'directory' ;
    public const string EXTENSION = 'extension' ;
    public const string NAME      = 'name' ;
    public const string PATH      = 'path' ;
}