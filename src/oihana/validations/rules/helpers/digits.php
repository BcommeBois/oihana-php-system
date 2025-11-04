<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;

/**
 * Generates the 'digits:value' rule expression.
 *
 * The field under validation must be numeric and must have an exact length of value.
 *
 * @param int $value
 *
 * @return string
 */
function digits( int $value ) :string
{
   return Rules::DIGITS . Char::COLON . $value ;
}