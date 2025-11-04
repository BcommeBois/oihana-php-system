<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'after:date' rule expression.
 *
 * The parameter should be any valid string that can be parsed by {@see strtotime}.
 *
 * For example:
 * - after:next week
 * - after:2016-12-31
 * - after:2016
 * - after:2016-12-31 09:56:02
 *
 * @param string $date The date format pattern.
 *
 * @return string
 */
function after( string $date ) :string
{
   return compile( [ Rules::AFTER , $date ] , Char::COLON  ) ;
}