<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'not_in:value1,value2,...' rule expression.
 *
 * The field under this rule must not be included in the given list of values.
 *
 * This rule also uses in_array and can have strict checks enabled the same way as In.
 *
 * @param string ...$values
 *
 * @return string
 */
function notIn( string ...$values ) :string
{
   return compile
   ([
       Rules::NOT_IN ,
       compile( $values , Char::COMMA )
   ]
   , Char::COLON ) ;
}