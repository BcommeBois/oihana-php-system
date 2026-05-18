<?php

namespace oihana\validations\rules ;

use oihana\validations\enums\Rules;
use Somnambulist\Components\Validation\Rule;
use Stringable;

use function org\iso\helpers\isIso8601DateTime;

/**
 * Validates whether a value is a valid ISO 8601 date-time expression.
 *
 * Accepted shape:
 * - Date  : `YYYY-MM-DD` (extended)
 * - Sep.  : `T` (mandatory in strict mode, space allowed when `$strict = false`)
 * - Time  : `HH:MM:SS`, optionally with fractional seconds (`.fff...`)
 * - Offset: optional `Z`, `±HH:MM` or `±HHMM`
 *
 * Calendar validity is checked (February 30 is rejected, leap years honored).
 *
 * ✅ Supported examples (strict mode, the default):
 * ```
 * 2026-05-14T08:15:30Z
 * 2026-05-14T08:15:30+02:00
 * 2026-05-14T08:15:30.123Z
 * ```
 *
 * ❌ Invalid examples (strict mode):
 * ```
 * 2026-05-14            // date only — use ISO8601DateRule
 * 2026-05-14 08:15:30   // space separator (allowed when strict = false)
 * 2026-02-30T00:00:00Z  // invalid calendar date
 * ```
 *
 * Empty / null values pass — the rule is "shape" only, declare `Rules::REQUIRED`
 * separately when the field is mandatory.
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class ISO8601DateTimeRule extends Rule
{
    /**
     * Creates a new ISO8601DateTimeRule instance.
     *
     * @param bool        $strict  When true (default), the `T` separator is mandatory.
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
     * @see Rules::ISO8601_DATE_TIME
     */
    public const string NAME = Rules::ISO8601_DATE_TIME ;

    /**
     * The message pattern used when the rule fails.
     * @var string
     */
    protected string $message = "The :attribute is not a valid ISO 8601 date-time expression." ;

    /**
     * Whether the rule runs in strict mode (T separator mandatory).
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

        return isIso8601DateTime( (string) $value , $this->strict ) ;
    }
}