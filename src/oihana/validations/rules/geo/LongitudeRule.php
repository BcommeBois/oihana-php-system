<?php

namespace oihana\validations\rules\geo ;

use oihana\validations\rules\abstracts\AbstractRangeRule;

/**
 * Validates that a value represents a valid geographic latitude.
 *
 * The latitude must be a numeric value between -90 and 90 degrees (inclusive).
 *
 * @example
 * ```php
 * use oihana\validations\rules\LatitudeRule;
 *
 * $rule = new LongitudeRule();
 *
 * $rule->check(  100  ) ;  // true
 * $rule->check( -100  ) ;  // true
 * $rule->check(  190  ) ;  // false
 * $rule->check( 'foo' ) ;  // false
 * $rule->check( null  ) ;  // false
 * ```
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class LongitudeRule extends AbstractRangeRule
{
    /**
     * The default error message for invalid longitude values.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be a valid longitude between -180 and 180 degrees.';

    protected float|int $max =  180 ;
    protected float|int $min = -180 ;
}