<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

class ArithmeticOperator
{
    use ConstantsTrait ;

    public const string ADDITION       = "+" ;
    public const string DIVISION       = "/" ;
    public const string EXPONENT       = "**" ;
    public const string MODULO         = "%" ;
    public const string MULTIPLICATION = "*" ;
    public const string SUBSTRACTION   = "-" ;
}