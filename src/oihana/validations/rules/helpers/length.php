<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;

/**
 * Generates the 'length:number' rule expression.
 *
 * The field under this validation must be a string of exactly the length specified.
 *
 * @param string|int $value
 *
 * @return string
 */
function length( string|int $value ) :string
{
   return Rules::LENGTH . Char::COLON . $value ;
}