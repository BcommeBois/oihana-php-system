<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use function oihana\core\strings\compile;

/**
 * Generates a 'rule_name[:value1,value2,...]' rule expression.
 *
 * @param string $name      The name of the rule
 * @param mixed ...$values  The optional values to passed-in.
 *
 * @return string
 */
function rule( string $name , mixed ...$values ) :string
{
    return compile( [ $name , compile( $values , Char::COMMA ) ] , Char::COLON ) ;
}