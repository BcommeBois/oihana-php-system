<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'between:min,max' rule expression.
 *
 * The field under this rule must have a size between min and max params.
 * Value size is calculated in the same way as min and max rule.
 *
 * You can also validate the maximum size of uploaded files using this rule:
 * ```php
 * $validation = $validator->validate
 * ([
 *    'photo' => $_FILES['photo']
 * ],
 * [
 *     'photo' => 'required|between:1M,2M'
 * ]);
 * ```
 *
 * @param string|int|float $min
 * @param string|int|float $max
 *
 * @return string
 */
function between( string|int|float $min , string|int|float $max ) :string
{
   return Rules::BETWEEN . Char::COLON . compile( [ $min , $max ] , Char::COMMA );
}