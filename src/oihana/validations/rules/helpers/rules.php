<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use function oihana\core\strings\compile;

/**
 * Generates a concatenated validation rules string from multiple rules.
 *
 * This helper allows you to pass a list of rules as strings or arrays and
 * concatenates them using the pipe (`|`) character, which is commonly used
 * in validation libraries (like Somnambulist Validation or Laravel) to
 * separate multiple validation constraints.
 *
 * @param string|array ...$rules One or more rules to combine.
 *
 * @return string
 *
 * @example
 * ```php
 * use function oihana\validations\rules\helpers\rules;
 *
 * // Pass rules as an array
 * $rules = rules([ 'required', 'min:5', 'max:10' ]);
 * // Returns: 'required|min:5|max:10'
 *
 * // Pass rules as separate arguments
 * $rules = rules('required', 'min:5', 'max:10');
 * // Returns: 'required|min:5|max:10'
 *
 * // Mixed usage
 * $rules = rules('required', ['min:5', 'max:10']);
 * // Returns: 'required|min:5|max:10'
 * ```
 * @see compile() Helper used internally to join rules.
 */
function rules( string|array ...$rules ) :string
{
   return compile( $rules , Char::PIPE ) ;
}