<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'mimes:extension_a,extension_b,...' rule expression.
 *
 * The $_FILES item under validation must have a MIME type corresponding to one of the listed extensions.
 *
 * This works on file extension and not client sent headers or embedded file type.
 *
 * If you require strict mime type validation you are recommended to implement
 * a custom MimeTypeGuesser that uses a full mime-type lookup library and replace the built-in mime rule.
 *
 * @param string ...$values
 *
 * @return string
 */
function mimes( string ...$values ) :string
{
   return compile
   ([
       Rules::MIMES ,
       compile( $values , Char::COMMA )
   ]
   , Char::COLON ) ;
}