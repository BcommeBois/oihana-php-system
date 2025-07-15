<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

class JsonParam
{
    use ConstantsTrait ;

    public const string ASSOCIATIVE = 'associative' ;
    public const string DEPTH       = 'depth' ;
    public const string FLAGS       = 'flags' ;
}