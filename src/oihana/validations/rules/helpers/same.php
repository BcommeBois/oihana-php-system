<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'same:anotherField' rule expression.
 *
 * The field value under this rule must have the same value as another_field.
 *
 * @param string $anotherField The another field to evaluates.
 *
 * @return string
 */
function same( string $anotherField ) :string
{
   return compile( [ Rules::SAME , $anotherField ] , Char::COLON  ) ;
}