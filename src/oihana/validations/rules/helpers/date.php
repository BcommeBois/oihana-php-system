<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'date[:format]' rule expression.
 *
 * The field under this rule must be valid date following a given format.
 * Parameter format is optional, default format is Y-m-d.
 *
 * @param string|null $format The date format pattern (Default Y-m-d)
 *
 * @return string
 */
function date( ?string $format = null ) :string
{
   return compile( [ Rules::DATE , $format ] , Char::COLON  ) ;
}