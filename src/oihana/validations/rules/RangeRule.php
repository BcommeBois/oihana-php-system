<?php

namespace oihana\validations\rules ;

use oihana\validations\rules\abstracts\AbstractRangeRule;
use Somnambulist\Components\Validation\Exceptions\ParameterException;
use function oihana\core\toNumber;

/**
 * Validates that a numeric value lies between a minimum and a maximum value (inclusive).
 *
 * The min and max values are provided as parameters.
 *
 * Example usage:
 * ```php
 * use oihana\validations\rules\RangeRule;
 *
 * $rule = new RangeRule();
 * $rule->fillParameters(['min' => 0, 'max' => 100]);
 *
 * $rule->check(50);  // true
 * $rule->check(101); // false
 * ```
 *
 * When used in a validation array:
 * ```php
 * 'score' => 'range:0,100',
 * ```
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class RangeRule extends AbstractRangeRule
{
    /**
     * The 'max' parameter.
     */
    public const string MAX = 'max' ;

    /**
     * The 'min' parameter.
     */
    public const string MIN = 'min' ;

    /**
     * Parameter names expected by this rule.
     *
     * @var string[]
     */
    protected array $fillableParams = ['min', 'max'];

    /**
     * Default error message.
     */
    protected string $message = 'The :attribute must be between :min and :max.';

    /**
     * Check if the given value is within the min/max parameters.
     *
     * @throws ParameterException
     */
    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters([ self::MIN , self::MAX ] ) ;

        $min = toNumber( $this->parameter(self::MIN ) ) ;
        $max = toNumber( $this->parameter(self::MAX ) ) ;

        if ( $min === false || $max === false )
        {
            throw new ParameterException('The "between" rule requires numeric min and max values.' ) ;
        }

        $this->min = $min ;
        $this->max = $max ;

        return parent::check( $value ) ;
    }
}