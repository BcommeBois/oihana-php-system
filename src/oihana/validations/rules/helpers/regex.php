<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'regex:/your-regex/' rule expression.
 *
 * The field under this rule must match the given regex.
 *
 * Note: if you require the use of |, then the regex rule must be written in array format instead of as a string.
 * For example:
 * ```php
 * use Somnambulist\Components\Validation\Factory;
 *
 * $validation = new Factory()->validate
 * (
 *    [ 'field' => 'value' ] ,
 *    [
 *       'field' =>
 *       [
 *          'required' ,
 *          'regex' => '/(this|that|value)/'
 *       ]
 *     ]
 * )
 * ```
 *
 * @param string $regex
 *
 * @return string
 */
function regex( string $regex ) :string
{
   return compile( [ Rules::REGEX , $regex ] , Char::COLON  ) ;
}