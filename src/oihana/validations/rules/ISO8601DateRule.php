<?php

namespace oihana\validations\rules ;

use Somnambulist\Components\Validation\Rule;
use Stringable;

/**
 * Validates whether a given value is a valid ISO 8601 date or datetime expression.
 *
 * This rule uses a comprehensive regular expression to match ISO 8601 formats,
 * including date-only, week date, ordinal date, and datetime with timezone offsets.
 *
 * ✅ Supported examples:
 * ```
 * 2025-10-13
 * 2025-10-13T18:25:43Z
 * 2025-10-13T18:25:43+02:00
 * 2025-286T23:59:59.999Z
 * 2025-W41-1
 * ```
 *
 * ❌ Invalid examples:
 * ```
 * 13/10/2025
 * 2025-13-40
 * 2025-10-13 99:99
 * ```
 *
 * @package oihana\api\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class ISO8601DateRule extends Rule
{
    /**
     * Constructor.
     *
     * @param string|null $pattern Optional custom pattern.
     * @param string|null $message Optional custom error message.
     */
    public function __construct(?string $pattern = null, ?string $message = null)
    {
        $this->pattern = $pattern ?? self::DEFAULT_PATTERN ;
        if ($message) {
            $this->message = $message;
        }
    }

    /**
     * The default pattern regexp expression of the rule.
     */
    public const string DEFAULT_PATTERN = '/^([+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24:?00)([.,]\d+(?!:))?)?(\17[0-5]\d([.,]\d+)?)?([zZ]|([+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/' ;

    /**
     * The message pattern if the rule is not valid.
     * @var string
     */
    protected string $message = "The :attribute is not a valid ISO8601 date expression.";

    /**
     * The regular expression pattern used for validation.
     * @var string
     */
    protected string $pattern;

    /**
     * Check if the value is valid.
     * @param $value
     * @return bool
     */
    public function check( $value ): bool
    {
        if ( $value === null || $value === '' )
        {
            return false ;
        }

        if ( !is_string( $value ) && !$value instanceof Stringable )
        {
            return false ;
        }

        return preg_match( $this->pattern , (string) $value ) === 1 ;
    }
}