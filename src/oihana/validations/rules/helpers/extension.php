<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'extension:extension_a,extension_b,...' rule expression.
 *
 * The field under this rule must end with an extension corresponding to one of those listed.
 *
 * This is useful for validating a file type for a given path or url.
 * The mimes rule should be used for validating uploads.
 *
 * If you require strict mime checking you should implement a custom MimeTypeGuesser
 * that can make use of a server side file checker that uses a mime library.
 *
 * @param string ...$values
 *
 * @return string
 */
function extension( string ...$values ) :string
{
   return compile
   ([
       Rules::EXTENSION ,
       compile( $values , Char::COMMA )
   ]
   , Char::COLON ) ;
}