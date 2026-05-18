<?php

namespace oihana\validations\rules ;

use oihana\validations\enums\Rules;
use Somnambulist\Components\Validation\Rule;
use Stringable;

use function org\iso\helpers\isIso8601Duration;

/**
 * Validates whether a value is a valid ISO 8601 duration string.
 *
 * The ISO 8601 duration format follows the pattern :
 * `P[n]Y[n]M[n]W[n]DT[n]H[n]M[n]S`
 *
 * ✅ Supported examples:
 * ```
 * P1Y2M3D       // 1 year, 2 months, 3 days
 * PT4H30M       // 4 hours, 30 minutes
 * P1W           // 1 week
 * P0D           // zero duration
 * P30D
 * ```
 *
 * ❌ Invalid examples:
 * ```
 * P             // no components
 * 1Y2M          // missing P
 * P1.5Y         // decimals rejected in strict mode
 * ```
 *
 * Modes:
 * - `$strict = true` (default) — regex-based validation, mandates at least one
 *   component and rejects decimals.
 * - `$strict = false`          — delegates to PHP's `DateInterval` parser, which
 *   is more permissive.
 *
 * Empty / null values pass — the rule is "shape" only, declare `Rules::REQUIRED`
 * separately when the field is mandatory.
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class ISO8601DurationRule extends Rule
{
    /**
     * Creates a new ISO8601DurationRule instance.
     *
     * @param bool        $strict  When true (default), uses regex validation
     *                             instead of DateInterval.
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
     * @see Rules::ISO8601_DURATION
     */
    public const string NAME = Rules::ISO8601_DURATION ;

    /**
     * The message pattern used when the rule fails.
     * @var string
     */
    protected string $message = "The :attribute is not a valid ISO 8601 duration expression." ;

    /**
     * Whether the rule runs in strict (regex) mode.
     * @var bool
     */
    protected bool $strict ;

    /**
     * Checks whether the given value satisfies the rule.
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

        return isIso8601Duration( (string) $value , $this->strict ) ;
    }
}