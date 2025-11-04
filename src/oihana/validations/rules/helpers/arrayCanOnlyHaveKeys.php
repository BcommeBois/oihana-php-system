<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'array_can_only_have_keys:value1,value2,...' rule expression.
 *
 * The array can only contain the specified keys, any keys not present will fail validation.
 * By default, associative data has no restrictions on the key => values that can be present.
 * For example: you have filters for a search box that are passed to SQL,
 * only the specified keys should be allowed to be sent and not any value in the array of filters.
 *
 * This rule is best used in conjunction with the array rule, though it can be used standalone.
 *
 * ```php
 * use Somnambulist\Components\Validation\Factory;
 *
 * $validation = $factory->validate
 * ([
 *     'filters' => ['foo' => 'bar', 'baz' => 'example']
 * ],
 * [
 *     'filters' => 'array|array_can_only_have_keys:foo,bar',
 * ]);
 *
 * $validation->passes(); // true if filters only has the keys in array_can_only_have_keys
 * ```
 *
 * @param string ...$values
 *
 * @return string
 */
function arrayCanOnlyHaveKeys( string ...$values ) :string
{
   return compile
   ([
       Rules::ARRAY_CAN_ONLY_HAVE_KEYS ,
       compile( $values , Char::COMMA )
   ]
   , Char::COLON ) ;
}