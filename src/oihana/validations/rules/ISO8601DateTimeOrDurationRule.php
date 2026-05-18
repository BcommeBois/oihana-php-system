<?php

namespace oihana\validations\rules ;

use oihana\validations\enums\Rules;
use Somnambulist\Components\Validation\Rule;
use Stringable;

use function org\iso\helpers\isIso8601DateTime;
use function org\iso\helpers\isIso8601Duration;

/**
 * Validates whether a value is either a valid ISO 8601 date-time or
 * a valid ISO 8601 duration string.
 *
 * Useful for fields whose semantics accept both an absolute deadline (e.g.
 * `2027-01-01T00:00:00Z`) and a relative offset (e.g. `P30D`, `P1Y`, `PT1H`).
 * Consumers typically resolve any duration into an absolute date at write
 * time so storage stays normalised.
 *
 * Composition:
 * - Absolute date-time : delegated to {@see isIso8601DateTime()}
 * - Duration           : delegated to {@see isIso8601Duration()}
 *
 * Pure ISO 8601 calendar dates (`YYYY-MM-DD` without a time component) are
 * **not** accepted — combine with {@see ISO8601DateRule} via a sibling
 * Somnambulist rule chain when the field also accepts dates.
 *
 * Empty / null values pass — the rule is "shape" only, declare `Rules::REQUIRED`
 * separately when the field is mandatory.
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class ISO8601DateTimeOrDurationRule extends Rule
{
    /**
     * Creates a new ISO8601DateTimeOrDurationRule instance.
     *
     * @param bool        $strict  When true (default), the `T` separator is
     *                             mandatory on the date-time branch and the
     *                             duration branch uses regex validation.
     * @param string|null $message Optional custom error message.
     */
    public function __construct( bool $strict = true , ?string $message = null )
    {
        $this->strict = $strict ;
        if ( $message )
        {
            $this->message = $message ;
        }
    }

    /**
     * The rule name, as registered in the validation factory.
     * @see Rules::ISO8601_DATE_TIME_OR_DURATION
     */
    public const string NAME = Rules::ISO8601_DATE_TIME_OR_DURATION ;

    /**
     * The message pattern used when both branches reject the value.
     * @var string
     */
    protected string $message = "The :attribute must be either an ISO 8601 date-time (e.g. 2027-01-01T00:00:00Z) or an ISO 8601 duration (e.g. P30D, P1Y, PT1H)." ;

    /**
     * Whether the rule runs in strict mode on both branches.
     * @var bool
     */
    protected bool $strict ;

    /**
     * Checks whether the given value satisfies one of the two accepted shapes.
     *
     * @param mixed $value
     * @return bool
     */
    public function check( mixed $value ) :bool
    {
        if ( $value === null || $value === '' )
        {
            return true ;
        }

        if ( !is_string( $value ) && !$value instanceof Stringable )
        {
            return false ;
        }

        $value = (string) $value ;

        if ( isIso8601Duration( $value , $this->strict ) )
        {
            return true ;
        }

        return isIso8601DateTime( $value , $this->strict ) ;
    }
}