<?php

namespace oihana\routes\enums;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Special response indicators
 */
class NoBodyResponse
{
    use ConstantsTrait ;

    const string DEFAULT = 'noBodyResponse'    ;
    const string CLAZZ   =  ':noBodyResponse'  ;
}