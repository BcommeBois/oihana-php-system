<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'in:value1,value2,...' rule expression.
 *
 * The field under this rule must be included in the given list of values.
 *
 * To help build the string rule, the In (and NotIn) rules have a helper method:
 * ```php
 * use Somnambulist\Components\Validation\Factory;
 * use Somnambulist\Components\Validation\Rules\In;
 *
 * $factory = new Factory();
 * $validation = $factory->validate( $data,
 * [
 *    'enabled' =>
 *    [
 *        'required',
 *        In::make([true, 1])
 *    ]
 * ]);
 * ```
 *
 * @param string ...$values
 *
 * @return string
 */
function in( string ...$values ) :string
{
   return compile
   ([
       Rules::IN ,
       compile( $values , Char::COMMA )
   ]
   , Char::COLON ) ;
}