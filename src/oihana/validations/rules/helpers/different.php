<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'different:anotherField' rule expression.
 *
 * Opposite of same; the field value under this rule must be different to another_field value.
 *
 * @param string $anotherField The another field to evaluates.
 *
 * @return string
 */
function different( string $anotherField ) :string
{
   return compile( [ Rules::DIFFERENT , $anotherField ] , Char::COLON  ) ;
}