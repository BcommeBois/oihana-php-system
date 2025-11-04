<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'required_without_all:field1,field2,...' rule expression.
 *
 * The field under validation must be present
 * and not empty only when all the other specified fields are not present.
 *
 * @param string ...$fields
 *
 * @return string
 */
function requiredWithoutAll( string ...$fields ) :string
{
   return compile
   ([
       Rules::REQUIRED_WITHOUT_ALL ,
       compile( $fields , Char::COMMA )
   ]
   , Char::COLON ) ;
}