<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'requires:field1,field2,...' rule expression.
 *
 * The field under validation requires that the specified
 * fields are present in the input data and are not empty.
 *
 * For example: field b "requires:a"; if a is either not present, or has an "empty" value,
 * then the validation fails. "empty" is false, empty string, or null.
 *
 * This is an extension of required_with, however the rule will fail when used with sometimes or nullable.
 * For example: if b "requires:a" and "a" is allowed to be nullable,
 * b will fail as it explicitly requires a with a value.
 *
 * @param string ...$fields
 * @return string
 */
function requires( string ...$fields ) :string
{
   return compile
   ([
       Rules::REQUIRES ,
       compile( $fields , Char::COMMA )
   ]
   , Char::COLON ) ;
}