<?php

namespace oihana\date;

use oihana\enums\Char;

/**
 * Helper class to manipulate and format time intervals (durations).
 *
 * Supports various input formats:
 * - Numeric values (int|float) as seconds
 * - Colon-separated strings: "MM:SS" or "HH:MM:SS"
 * - Human-readable units: "1.5d 3h 15m 12.5s"
 *
 * Useful for displaying or converting durations to readable forms,
 * such as `"2h 5m"` or `"2:05:00"`, or for computing the total seconds or minutes.
 *
 * ### Basic usage:
 * ```php
 * $duration = new TimeInterval('7:31');
 * echo $duration->humanize();         // 7m 31s
 * echo $duration->formatted();        // 7:31
 * echo $duration->toSeconds();        // 451
 * echo $duration->toMinutes();        // 7.5166
 * echo $duration->toMinutes(null, 0); // 8
 * ```
 *
 * ### With hour/minute/second string:
 * ```php
 * $duration = new TimeInterval('1h 2m 5s');
 * echo $duration->humanize();  // 1h 2m 5s
 * echo $duration->formatted(); // 1:02:05
 * echo $duration->toSeconds(); // 3725
 * ```
 *
 * ### With days and custom hours/day:
 * ```php
 * $duration = new TimeInterval('1.5d 1.5h 2m 5s', 6);
 * echo $duration->humanize();  // 1d 4h 32m 5s
 * echo $duration->formatted(); // 10:32:05
 * echo $duration->toMinutes(); // 632.083
 * ```
 *
 * ### Raw seconds:
 * ```php
 * $duration = new TimeInterval(4293);
 * echo $duration->humanize() ;  // 1h 11m 33s
 * echo $duration->formatted() ; // 1:11:33
 * ```
 *
 * @package oihana\date
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class TimeInterval
{
    /**
     * Creates a new TimeInterval instance.
     *
     * The $duration parameter can be:
     * - an integer or float representing the duration in seconds,
     * - a string formatted as "HH:MM", "HH:MM:SS",
     *   or a string containing time units (e.g. "1h 30m 15s", "2.5d 4h"),
     * - or null for an initial zero duration.
     *
     * The $hoursPerDay parameter sets the number of hours
     * in a day for calculations involving days (default is 24).
     *
     * @param int|float|string|null $duration Initial duration to parse or null for zero.
     * @param int $hoursPerDay Number of hours per day (used for day-to-hour conversions).
     */
    public function __construct( int|float|string|null $duration = null, int $hoursPerDay = 24 )
    {
        $this->reset();
        $this->hoursPerDay = $hoursPerDay;
        if ( $duration !== null )
        {
            $this->parse( $duration );
        }
    }

    /**
     * The whole-day component of the parsed duration.
     * Readable from outside, but only mutable from within the class.
     * @var int|float|null
     */
    public private(set) int|float|null $days = 0 ;

    /**
     * The whole-hour component of the parsed duration (after days).
     * Readable from outside, but only mutable from within the class.
     * @var int|float|null
     */
    public private(set) int|float|null $hours = 0 ;

    /**
     * The number of hours in a single day, used for day↔hour conversions.
     * Readable from outside, but only mutable from within the class.
     * @var int|null
     */
    public private(set) int|null $hoursPerDay = 24 ;

    /**
     * The whole-minute component of the parsed duration (after hours).
     * Readable from outside, but only mutable from within the class.
     * @var int|float|null
     */
    public private(set) int|float|null $minutes = 0 ;

    /**
     * The seconds component of the parsed duration (may carry a fractional part).
     * Readable from outside, but only mutable from within the class.
     * @var int|float|null
     */
    public private(set) int|float|null $seconds = 0.0 ;

    /**
     * Returns the duration as a colon-formatted string.
     *
     * For example, one hour and 42 minutes returns `"1:42"`.
     * With `$zeroFill` set to `true`:
     *   - 42 minutes returns `"0:42:00"`
     *   - 28 seconds returns `"0:00:28"`
     *
     * Note: when `$duration` is not `null`, the instance is re-parsed via
     * {@see self::parse()} — its internal state is mutated as a side-effect.
     *
     * @param  int|float|string|null $duration Optional duration to parse before formatting.
     * @param  bool $zeroFill Force zero-fill of the hour and minute components.
     * @return string The colon-formatted duration string.
     */
    public function formatted( int|float|string|null $duration = null, bool $zeroFill = false ) :string
    {
        if ( $duration !== null )
        {
            $this->parse( $duration ) ;
        }

        $output = Char::EMPTY ;

        $hours = $this->hours + ( $this->days * $this->hoursPerDay );

        if ( $this->seconds > 0 )
        {
            $output .= ( ( $this->seconds < 10 && ( $this->minutes > 0 || $hours > 0 || $zeroFill ) ) ? '0' : '' ) . $this->seconds ;
        }
        else
        {
            $output = ( $this->minutes > 0 || $hours > 0 || $zeroFill ) ? '00' : '0' ;
        }

        if ( $this->minutes > 0)
        {
            if ($this->minutes <= 9 && ($hours > 0 || $zeroFill))
            {
                $output = '0' . $this->minutes . Char::COLON . $output;
            }
            else
            {
                $output = $this->minutes . Char::COLON . $output;
            }
        }
        else if ( $hours > 0 || $zeroFill )
        {
            $output = '00' . Char::COLON . $output;
        }

        if ( $hours > 0 )
        {
            $output = $hours . Char::COLON . $output ;
        }
        else if ( $zeroFill )
        {
            $output = '0' . Char::COLON . $output ;
        }

        return $output ;
    }

    /**
     * Returns the duration as a human-readable string.
     *
     * For example, one hour and 42 minutes returns `"1h 42m"`.
     *
     * Note: when `$duration` is not `null`, the instance is re-parsed via
     * {@see self::parse()} — its internal state is mutated as a side-effect.
     *
     * @param  int|float|string|null $duration Optional duration to parse before formatting.
     * @return string The human-readable duration string (e.g. `"1d 2h 3m 4s"`).
     */
    public function humanize( int|float|string|null $duration = null ) :string
    {
        if ( $duration !== null )
        {
            $this->parse( $duration );
        }

        $output = Char::EMPTY ;

        if ($this->seconds > 0 || ($this->seconds === 0.0 && $this->minutes === 0 && $this->hours === 0 && $this->days === 0))
        {
            $output .= $this->seconds . 's' ;
        }

        if ($this->minutes > 0)
        {
            $output = $this->minutes . 'm ' . $output;
        }

        if ($this->hours > 0)
        {
            $output = $this->hours . 'h ' . $output;
        }

        if ($this->days > 0)
        {
            $output = $this->days . 'd ' . $output ;
        }

        return trim( $output );
    }

    /**
     * Parses one of the supported duration forms and updates the instance.
     *
     * Supported forms:
     * - `null` → returns `false` (no-op other than {@see self::reset()}).
     * - Numeric (int|float) → interpreted as a total number of seconds.
     * - Colon-separated string `"MM:SS"` or `"HH:MM:SS"`.
     * - Unit-suffixed string `"1.5d 3h 15m 12.5s"` (any subset, any order).
     *
     * The instance is {@see self::reset()} before being populated. On success
     * the instance itself is returned (fluent style); on failure `false` is
     * returned and the instance is left in its reset state.
     *
     * @param  int|float|string|null $duration The duration to parse.
     * @return self|bool The instance on success, or `false` if the input cannot be parsed.
     */
    public function parse( int|float|string|null $duration ) :self|bool
    {
        $this->reset();

        if ( $duration === null )
        {
            return false ;
        }

        if ( is_numeric( $duration ) )
        {
            $this->seconds = (float) $duration;

            if ($this->seconds >= 60)
            {
                $this->minutes = (int) floor($this->seconds / 60 ) ;

                // count current precision
                $precision = 0;
                if ( ($delimiterPos = strpos((string)$this->seconds, Char::SPACE ) ) !== false)
                {
                    $precision = strlen(substr((string)$this->seconds, $delimiterPos + 1));
                }

                $this->seconds = round(($this->seconds - ($this->minutes * 60)), $precision) ;
            }

            if ( $this->minutes >= 60 )
            {
                $this->hours = (int) floor($this->minutes / 60) ;
                $this->minutes = (int) ($this->minutes - ($this->hours * 60)) ;
            }

            if ($this->hours >= $this->hoursPerDay)
            {
                $this->days = (int)floor($this->hours / $this->hoursPerDay);
                $this->hours = (int)($this->hours - ($this->days * $this->hoursPerDay));
            }

            return $this;
        }

        if ( str_contains( $duration, Char::COLON ) )
        {
            $parts = explode(Char::COLON , $duration ) ;
            if ( count( $parts ) == 2 )
            {
                $this->minutes =   (int) $parts[0] ;
                $this->seconds = (float) $parts[1] ;
            }
            else
            {
                if ( count($parts) == 3 )
                {
                    $this->hours   =   (int) $parts[0] ;
                    $this->minutes =   (int) $parts[1] ;
                    $this->seconds = (float) $parts[2] ;
                }
            }
            return $this ;
        }

        if
        (
            preg_match( $this->daysRegex    , $duration ) ||
            preg_match( $this->hoursRegex   , $duration ) ||
            preg_match( $this->minutesRegex , $duration ) ||
            preg_match( $this->secondsRegex , $duration )
        )
        {
            if (preg_match($this->daysRegex, $duration, $matches))
            {
                $num = $this->numberBreakdown((float) $matches[1]);
                $this->days += (int)$num[0];
                $this->hours += $num[1] * $this->hoursPerDay;
            }

            if (preg_match($this->hoursRegex, $duration, $matches))
            {
                $num = $this->numberBreakdown((float) $matches[1]);
                $this->hours += (int)$num[0];
                $this->minutes += $num[1] * 60;
            }

            if (preg_match($this->minutesRegex, $duration, $matches))
            {
                $this->minutes += (int)$matches[1];
            }

            if (preg_match($this->secondsRegex, $duration, $matches))
            {
                $this->seconds += (float)$matches[1];
            }

            return $this;
        }

        return false;
    }

    /**
     * Resets the duration components to zero.
     *
     * The {@see self::$hoursPerDay} setting is preserved.
     *
     * @return void
     */
    public function reset() :void
    {
        $this->seconds = 0.0 ;
        $this->minutes = 0 ;
        $this->hours   = 0 ;
        $this->days    = 0 ;
    }

    /**
     * Returns the duration as a total number of minutes.
     *
     * For example, one hour and 42 minutes returns `102`.
     *
     * Note: when `$duration` is not `null`, the instance is re-parsed via
     * {@see self::parse()} — its internal state is mutated as a side-effect.
     *
     * @param  int|float|string|null $duration Optional duration to parse before computing.
     * @param  int|bool $precision Number of decimal digits to round to; `false` to skip rounding,
     *                             `true` is equivalent to `0`.
     * @return int|float The total number of minutes (rounded according to `$precision`).
     */
    public function toMinutes( int|float|string|null $duration = null, int|bool $precision = false ) :int|float
    {
        if (null !== $duration)
        {
            $this->parse($duration);
        }

        if ( $precision === true )
        {
            $precision = 0 ;
        }

        $output = ($this->days * $this->hoursPerDay * 60 * 60) + ($this->hours * 60 * 60) + ($this->minutes * 60) + $this->seconds;
        $result = intval($output) / 60;

        return $precision !== false ? round( $result , $precision ) : $result;
    }

    /**
     * Returns the duration as a total number of seconds.
     *
     * For example, one hour and 42 minutes returns `6120`.
     *
     * Note: when `$duration` is not `null`, the instance is re-parsed via
     * {@see self::parse()} — its internal state is mutated as a side-effect.
     *
     * @param  int|float|string|null $duration Optional duration to parse before computing.
     * @param  int|bool $precision Number of decimal digits to round to; `false` to skip rounding.
     * @return int|float The total number of seconds (rounded according to `$precision`).
     */
    public function toSeconds( int|float|string|null $duration = null , int|bool $precision = false ) :int|float
    {
        if ( $duration !== null )
        {
            $this->parse( $duration ) ;
        }
        $output = ($this->days * $this->hoursPerDay * 60 * 60) + ($this->hours * 60 * 60) + ($this->minutes * 60) + $this->seconds;
        return $precision !== false ? round( $output, $precision ) : $output;
    }

    /**
     * Regex matching the days component (e.g. `"1.5d"`).
     * @var string
     */
    private string $daysRegex = '/(\d+(?:\.\d+)?)\s*d/i' ;

    /**
     * Regex matching the hours component (e.g. `"3h"`, `"3.5h"`).
     * @var string
     */
    private string $hoursRegex = '/(\d+(?:\.\d+)?)\s*h/i' ;

    /**
     * Regex matching the minutes component (e.g. `"15m"`).
     * @var string
     */
    private string $minutesRegex = '/(\d+)\s*m/i' ;

    /**
     * Regex matching the seconds component (e.g. `"12s"`, `"12.5s"`).
     * @var string
     */
    private string $secondsRegex = '/(\d+(?:\.\d+)?)\s*s/i' ;

    /**
     * Splits a number into its integer part and its fractional part, preserving sign.
     *
     * @param  float $number The number to split.
     * @return array{0:float,1:float} A two-element array: `[integerPart, fractionalPart]`.
     */
    private function numberBreakdown( float $number ) : array
    {
        $negative = 1 ;
        if ( $number < 0 )
        {
            $negative = -1 ;
            $number  *= -1 ;
        }
        return [ floor($number) * $negative , ( $number - floor( $number ) ) * $negative ] ;
    }
}