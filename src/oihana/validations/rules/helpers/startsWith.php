<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'starts_with:anotherField' rule expression.
 *
 * The field under this validation must start with another_field.
 * Comparison can be against strings, numbers and array elements.
 *
 * @param string $anotherField The another field to evaluates.
 *
 * @return string
 */
function startsWith( string $anotherField ) :string
{
   return compile( [ Rules::STARTS_WITH , $anotherField ] , Char::COLON  ) ;
}