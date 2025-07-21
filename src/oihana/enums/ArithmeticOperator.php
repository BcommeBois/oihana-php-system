<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Class ArithmeticOperator
 *
 * Enumeration of common arithmetic operator symbols as string constants.
 *
 * This class provides symbolic names for basic arithmetic operations,
 * facilitating clearer, self-documenting code when performing or
 * representing arithmetic computations in string form.
 *
 * ### Operators Included:
 * - Addition (`+`)
 * - Subtraction (`-`)
 * - Multiplication (`*`)
 * - Division (`/`)
 * - Modulo (`%`)
 * - Exponentiation (`**`)
 *
 * ### Usage Example:
 * ```php
 * use oihana\enums\ArithmeticOperator;
 *
 * $expression = "3 " . ArithmeticOperator::ADDITION . " 4";  // "3 + 4"
 * $power = "2 " . ArithmeticOperator::EXPONENT . " 8";     // "2 ** 8"
 * ```
 *
 * ### Features:
 * - Constants defined as strings representing the actual operator symbols.
 * - Uses `ConstantsTrait` for reflection capabilities or retrieving constants dynamically.
 * - Useful in expression builders, parsers, or any code that manipulates arithmetic operators symbolically.
 *
 * @package oihana\enums
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
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