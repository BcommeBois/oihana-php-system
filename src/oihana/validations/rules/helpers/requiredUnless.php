<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'required_unless:anotherField,value1,value2,...' rule expression.
 *
 * The field under validation must be present and not empty unless the another_field field is equal to any value.
 *
 * @param string $anotherField The another field to evaluates.
 * @param mixed ...$values
 * @return string
 */
function requiredUnless( string $anotherField , mixed ...$values ) :string
{
   return compile
   ([
       Rules::REQUIRED_UNLESS ,
       compile( [ $anotherField , ...$values ] , Char::COMMA )
   ]
   , Char::COLON ) ;
}