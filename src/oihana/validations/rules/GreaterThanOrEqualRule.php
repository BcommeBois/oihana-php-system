<?php

namespace oihana\validations\rules ;

use oihana\validations\rules\abstracts\ComparisonRule;

/**
 * Validation rule that ensures a given value is **greater than or equal to** another field's value
 * or to a fixed numeric constant.
 *
 * This rule can be used to enforce constraints between numeric fields (e.g. durations, limits, quotas)
 * or to validate a value against a fixed threshold.
 *
 * ### Usage examples
 * ```php
 * // Compare with another field
 * $validation = $validator->validate($data,
 * [
 *     'implicitHybridTokenLifetime' => 'gte_field:maximumAccessTokenExpiration',
 * ]);
 *
 * // Compare with a fixed value
 * $validation = $validator->validate($data,
 * [
 *     'timeout' => 'gte_field:3600',
 * ]);
 * ```
 *
 * When validation fails, the default message is:
 * ```
 * The :attribute must be less than :comparison_field.
 * ```
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class GreaterThanOrEqualRule extends ComparisonRule
{
    protected string $message = 'The :attribute must be greater than or equal to :comparison_field.';

    protected function compare(float|int $a, float|int $b): bool
    {
        return $a >= $b;
    }
}