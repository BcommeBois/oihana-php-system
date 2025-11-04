<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'before:date' rule expression.
 *
 * The field under this rule must be a date before the given maximum.
 *
 * This also works the same way as the {@see after} rule. Pass anything that can be parsed by {@see strtotime}
 *
 * @param string $date The date format pattern.
 *
 * @return string
 *
 * @see after()
 */
function before( string $date ) :string
{
   return compile( [ Rules::BEFORE , $date ] , Char::COLON  ) ;
}