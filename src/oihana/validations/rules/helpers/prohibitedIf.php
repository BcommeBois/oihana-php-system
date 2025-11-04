<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'prohibited_if:anotherField' rule expression.
 *
 * The field under this rule is not allowed if another_field is provided with any of the value(s).
 *
 * @param string $anotherField The another field to evaluates.
 * @param mixed ...$values
 *
 * @return string
 */
function prohibitedIf( string $anotherField , mixed ...$values  ) :string
{
    return compile
    ([
        Rules::PROHIBITED_IF ,
        compile( [ $anotherField , ...$values ] , Char::COMMA )
    ]
    , Char::COLON ) ;
}