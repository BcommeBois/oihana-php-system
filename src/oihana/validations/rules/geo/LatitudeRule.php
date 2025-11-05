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
 * $rule = new LatitudeRule();
 *
 * $rule->check(45.0);       // true
 * $rule->check(-89.9999);   // true
 * $rule->check(91);         // false
 * $rule->check('foo');      // false
 * $rule->check(null);       // false
 * ```
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class LatitudeRule extends AbstractRangeRule
{
    /**
     * The default error message for invalid latitude values.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be a valid latitude between -90 and 90 degrees.';

    protected float|int $max = 90 ;
    protected float|int $min = -90;
}