<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;

/**
 * Generates the 'min:value' rule expression.
 *
 * The field under this rule must have a size greater than or equal to the given number.
 *
 * For string values, the size corresponds to the number of characters.
 * For integer or float values, size corresponds to its numerical value.
 * For an array, size corresponds to the count of the array.
 *
 * If your value is numeric string, you can use the numeric rule to treat
 * its size as a numeric value instead of the number of characters.
 *
 * You can also validate the minimum size of uploaded files using this rule:
 * ```php
 * $validation = $validator->validate
 * ([
 *     'photo' => $_FILES['photo']
 * ],
 * [
 *     'photo' => 'required|min:1M'
 * ]);
 * ```
 *
 * @param string|int|float $value
 *
 * @return string
 */
function min( string|int|float $value ) :string
{
   return Rules::MIN . Char::COLON . $value ;
}