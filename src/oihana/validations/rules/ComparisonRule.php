<?php

namespace oihana\validations\rules ;

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;
use function oihana\core\toNumber;

/**
 * Base class for numeric comparison rules.
 *
 * Provides common logic for comparing a field value against another field or a fixed numeric value.
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz
 * @since   1.0.0
 */
abstract class ComparisonRule extends Rule
{
    /**
     * The 'comparison_field' parameter.
     */
    public const string COMPARISON_FIELD = 'comparison_field';

    protected array $fillableParams = [ self::COMPARISON_FIELD ] ;

    /**
     * Performs the comparison between the attribute value and the comparison value.
     *
     * @param mixed $value The value of the current attribute being validated.
     *
     * @return bool True if the comparison succeeds, false otherwise.
     *
     * @throws ParameterException If the comparison parameter is missing.
     */
    public function check( mixed $value ): bool
    {
        $this->assertHasRequiredParameters( [ self::COMPARISON_FIELD ] );

        $comparisonFieldOrValue = $this->parameter(self::COMPARISON_FIELD );

        $comparisonValue = $this->getComparisonValue( $comparisonFieldOrValue );

        if ( $value === null || $comparisonValue === null )
        {
            return false ;
        }

        // Conversion en numérique pour la comparaison
        $numericValue           = toNumber( $value ) ;
        $numericComparisonValue = toNumber( $comparisonValue ) ;

        if ( $numericValue === false || $numericComparisonValue === false )
        {
            return false ;
        }

        return $this->compare( $numericValue , $numericComparisonValue ) ;
    }

    /**
     * Must be implemented by subclasses to perform the actual comparison.
     *
     * @param float|int $a The attribute value.
     * @param float|int $b The comparison value.
     *
     * @return bool True if the rule condition is met.
     */
    abstract protected function compare( float|int $a , float|int $b ) :bool;

    /**
     * Get the comparison value from either a field name or a direct value
     */
    protected function getComparisonValue( string $fieldOrValue ): mixed
    {
        if ( is_numeric( $fieldOrValue ) )
        {
            return $fieldOrValue ;
        }
        return $this->attribute()->value( $fieldOrValue ) ;
    }
}