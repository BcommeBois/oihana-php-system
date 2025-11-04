<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * The field under validation must be present and not empty only
 * if all the other specified fields are present.
 *
 * @param string ...$fields
 *
 * @return string
 */
function requiredWithAll( string ...$fields ) :string
{
   return compile
   ([
       Rules::REQUIRED_WITH_ALL ,
       compile( $fields , Char::COMMA )
   ]
   , Char::COLON ) ;
}