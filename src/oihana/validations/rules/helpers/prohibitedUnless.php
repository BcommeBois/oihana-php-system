<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'prohibited_unless:anotherField' rule expression.
 *
 * The field under this rule is not allowed unless another_field has one of these values. This is the inverse of prohibited_if.
 *
 * @param string $anotherField The another field to evaluates.
 * @param mixed ...$values
 *
 * @return string
 */
function prohibitedUnless( string $anotherField , mixed ...$values  ) :string
{
    return compile
    ([
        Rules::PROHIBITED_UNLESS ,
        compile( [ $anotherField , ...$values ] , Char::COMMA )
    ]
    , Char::COLON ) ;
}