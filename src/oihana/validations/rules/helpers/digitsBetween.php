<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;

/**
 * Generates the 'digits_between:min,max' rule expression.
 *
 * The field under validation must be numeric and have a length between the given min and max.
 *
 * @param int $min
 * @param int $max
 *
 * @return string
 */
function digitsBetween( int $min , int $max ) :string
{
   return Rules::DIGITS_BETWEEN . Char::COLON . $min . Char::COMMA . $max ;
}