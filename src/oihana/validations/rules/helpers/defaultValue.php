<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'default:value' rule expression.
 *
 * If the attribute has no value, this default will be used
 * in place in the validated data.
 *
 * For example if you have validation like this
 *
 * @param mixed $value The default value.
 *
 * @return string
 */
function defaultValue( mixed $value ) :string
{
   return compile( [ Rules::DEFAULT , $value ] , Char::COLON ) ;
}