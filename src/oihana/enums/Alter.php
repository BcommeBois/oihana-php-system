<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Enumeration of all alter filter to apply on a object property.
 *
 * @package oihana\enums
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class Alter
{
    use ConstantsTrait ;

    public const string ARRAY          = 'array' ;
    public const string CALL           = 'call' ;
    public const string CLEAN          = 'clean' ;
    public const string FLOAT          = 'float' ;
    public const string GET            = 'get' ;
    public const string LIST           = 'list' ;
    public const string INT            = 'int' ;
    public const string JSON_PARSE     = 'jsonParse' ;
    public const string JSON_STRINGIFY = 'jsonStringify' ;
    public const string URL            = 'url' ;
    public const string VALUE          = 'value' ;
}