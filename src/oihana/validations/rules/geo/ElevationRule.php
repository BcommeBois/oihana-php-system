<?php

namespace oihana\validations\rules\geo ;

use oihana\validations\rules\abstracts\AbstractRangeRule;

/**
 * Ensures that a value represents a valid elevation (altitude) in meters.
 *
 * The elevation must be a numeric value between -11500 (deepest ocean depth)
 * and +8900 (above Mount Everest), inclusive.
 *
 * @example
 * ```php
 * use oihana\validations\rules\ElevationRule;
 *
 * $rule = new ElevationRule();
 *
 * $rule->check(0);        // true  (sea level)
 * $rule->check(8848);     // true  (Mount Everest)
 * $rule->check(-10994);   // true  (Mariana Trench)
 * $rule->check(-12000);   // false (too deep)
 * $rule->check(9000);     // false (too high)
 * $rule->check('foo');    // false
 * ```
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class ElevationRule extends AbstractRangeRule
{
    /**
     * Minimum allowed elevation (in meters).
     */
    protected float|int $min = -11500;

    /**
     * Maximum allowed elevation (in meters).
     */
    protected float|int $max = 8900;

    /**
     * Custom error message.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be a valid elevation between -11500 and 8900 meters.';
}