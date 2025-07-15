<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * The boolean enumeration.
 */
class Boolean
{
    use ConstantsTrait ;

    public const string FALSE = 'false' ;
    public const string TRUE  = 'true'  ;
}


