<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'ends_with:anotherField' rule expression.
 *
 * The field under this validation must end with another_field.
 * Comparison can be against strings, numbers and array elements.
 *
 * @param string $anotherField The another field to evaluates.
 *
 * @return string
 */
function endsWith( string $anotherField ) :string
{
   return compile( [ Rules::ENDS_WITH , $anotherField ] , Char::COLON  ) ;
}