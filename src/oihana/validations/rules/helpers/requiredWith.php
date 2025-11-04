<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'required_with:field1,field2,...' rule expression.
 *
 * The field under validation must be present and not empty only
 * if any of the other specified fields are present.
 *
 * Note: the behaviour of this rule can be circumvented if the rule this
 * is defined on is sometimes or nullable.
 *
 * For example:
 * if a is "required_with:b", but a is also only sometimes present,
 * then the required_with will never trigger as the sometimes rule will negate it.
 * a would also need to be explicitly passed to trigger the rule.
 *
 * @param string ...$fields
 *
 * @return string
 */
function requiredWith( string ...$fields ) :string
{
   return compile
   ([
       Rules::REQUIRED_WITH ,
       compile( $fields , Char::COMMA )
   ]
   , Char::COLON ) ;
}