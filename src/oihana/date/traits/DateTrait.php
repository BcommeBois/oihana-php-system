<?php

namespace oihana\date\traits;

/**
 * The command to manage an ArangoDB database.
 */
trait DateTrait
{
    /**
     * The default date format.
     */
    public const string DEFAULT_DATE_FORMAT = 'Y-m-d\TH:i:s' ;

    /**
     * The default timezone.
     */
    public const string DEFAULT_TIMEZONE = 'Europe/Paris' ;

    /**
     * The default 'now' constant to defines the current date.
     */
    public const string NOW = 'now' ;

    /**
     * The date format of the dates.
     * @var string
     */
    public string $dateFormat = 'Y-m-d\TH:i:s' ;

    /**
     * The timezone of the date to backup the database.
     * @var ?string
     */
    public ?string $timezone = 'Europe/Paris' ;
}