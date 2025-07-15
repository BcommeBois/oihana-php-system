<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

class Order
{
    use ConstantsTrait ;

    /**
     * The ascending order (lower case).
     */
    public const string asc  = 'asc' ;

    /**
     * The ascending order (upper case).
     */
    public const string ASC  = 'ASC' ;

    /**
     * The descending order (lower case).
     */
    public const string desc = 'desc' ;

    /**
     * The descending order (upper case).
     */
    public const string DESC = 'DESC' ;
}
