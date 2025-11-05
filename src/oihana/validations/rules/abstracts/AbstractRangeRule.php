<?php

namespace oihana\validations\rules\abstracts ;

use Somnambulist\Components\Validation\Rule;
use function oihana\core\toNumber;

/**
 * Base class for validation rules ensuring a numeric value lies within a specific range.
 *
 * Extend this class to implement specific range-based validation rules,
 * e.g. latitude, longitude, or bounded numeric limits.
 *
 * Subclasses must define:
 * - `protected float|int $min` (the lower bound)
 * - `protected float|int $max` (the upper bound)
 *
 * @package oihana\validations\rules
 * @since   1.0.0
 */
abstract class AbstractRangeRule extends Rule
{
    /**
     * Lower numeric bound (inclusive).
     *
     * @var float|int
     */
    protected float|int $min;

    /**
     * Upper numeric bound (inclusive).
     *
     * @var float|int
     */
    protected float|int $max;

    /**
     * Default message template.
     *
     * @var string
     */
    protected string $message = 'The :attribute must be between :min and :max.';

    /**
     * Check if the given value lies within the defined range.
     */
    public function check(mixed $value): bool
    {
        if ( $value === null || $value === '' )
        {
            return false;
        }

        $numericValue = toNumber( $value ) ;

        if ( $numericValue === false )
        {
            return false;
        }

        return $numericValue >= $this->min && $numericValue <= $this->max ;
    }

    /**
     * Gets the minimum bound.
     */
    public function getMin(): float|int
    {
        return $this->min;
    }

    /**
     * Gets the maximum bound.
     */
    public function getMax(): float|int
    {
        return $this->max;
    }
}