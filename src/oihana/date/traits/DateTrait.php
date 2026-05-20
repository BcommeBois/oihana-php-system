<?php

namespace oihana\date\traits;

/**
 * Provides shared defaults (date format and timezone) for classes manipulating dates.
 *
 * @package oihana\date\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait DateTrait
{
    /**
     * The default date format (ISO-8601, no offset).
     */
    public const string DEFAULT_DATE_FORMAT = 'Y-m-d\TH:i:s' ;

    /**
     * The default timezone identifier used when none is configured.
     */
    public const string DEFAULT_TIMEZONE = 'Europe/Paris' ;

    /**
     * Sentinel value commonly used to denote the current date/time.
     */
    public const string NOW = 'now' ;

    /**
     * The date format used by the consuming class.
     * @var string
     */
    public string $dateFormat = self::DEFAULT_DATE_FORMAT ;

    /**
     * The timezone identifier used by the consuming class, or `null` to fall back to PHP's default.
     * @var ?string
     */
    public ?string $timezone = self::DEFAULT_TIMEZONE ;
}