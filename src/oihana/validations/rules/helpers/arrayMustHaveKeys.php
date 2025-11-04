<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'array_must_have_keys:value1,value2,...' rule expression.
 *
 * The array must contain all the specified keys to be valid.
 * This is useful to ensure that a nested array meets a prescribed format.
 * The same thing can be achieved by using individual rules for each key with required.
 * Note that this will still allow additional keys to be present,
 * it merely validates the presence of specific keys.
 *
 * This rule is best used in conjunction with the array rule,
 * though it can be used standalone.
 *
 * ```php
 * use Somnambulist\Components\Validation\Factory;
 *
 * $validation = $factory->validate
 * ([
 *     'filters' => ['foo' => 'bar', 'baz' => 'example']
 * ],
 * [
 *     'filters' => 'array|array_must_have_keys:foo,bar,baz',
 * ]);
 *
 * $validation->passes(); // true if filters has all the keys in array_must_have_keys
 * ```
 * The following examples are functionally equivalent:
 * ```php
 * use Somnambulist\Components\Validation\Factory;
 *
 * $validation = $factory->validate
 * ([
 *     'filters' => ['foo' => 'bar', 'baz' => 'example']
 * ],
 * [
 *     'filters'     => 'array|array_must_have_keys:foo,bar,baz',
 *     'filters.foo' => 'string|between:1,50',
 *     'filters.bar' => 'numeric|min:1',
 *     'filters.baz' => 'uuid',
 * ]);
 *
 * $validation = $factory->validate
 * ([
 * '    filters' => ['foo' => 'bar', 'baz' => 'example']
 * ],
 * [
 *     'filters'     => 'array',
 *     'filters.foo' => 'required|string|between:1,50',
 *     'filters.bar' => 'required|numeric|min:1',
 *     'filters.baz' => 'required|uuid',
 * ]);
 * ```
 *
 * @param string ...$values
 *
 * @return string
 */
function arrayMustHaveKeys( string ...$values ) :string
{
   return compile
   ([
       Rules::ARRAY_MUST_HAVE_KEYS ,
       compile( $values , Char::COMMA )
   ]
   , Char::COLON ) ;
}