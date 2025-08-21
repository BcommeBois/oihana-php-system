<?php

namespace oihana\logging;

use oihana\enums\Char;
use oihana\reflect\traits\ConstantsTrait;
use org\schema\Thing;

/**
 * @package oihana\logging
 */
class Log extends Thing
{
    use ConstantsTrait ;

    /**
     * The date of the log (YYYY-MM-DD).
     * @var string|null
     */
    public ?string $date ;

    /**
     * The level of the log (e.g., INFO, ERROR, DEBUG).
     * @var int|string|null
     */
    public int|string|null $level ;

    /**
     * The message of the log.
     * @var string|null
     */
    public ?string $message ;

    /**
     * The time of the log.
     * @var string|null
     */
    public ?string $time ;

    public const string DATE    = 'date' ;
    public const string LEVEL   = 'level' ;
    public const string MESSAGE = 'message' ;
    public const string TIME    = 'time' ;

    /**
     * Returns the array representation of the log definition.
     * @return array
     */
    public function toArray() : array
    {
        return
        [
            self::DATE    => $this->date    ?? null ,
            self::TIME    => $this->time    ?? null ,
            self::LEVEL   => $this->level   ?? null ,
            self::MESSAGE => $this->message ?? null ,
        ];
    }

    /**
     * Returns the string representation of the thing.
     * @return string
     */
    public function __toString():string
    {
        return implode( Char::SPACE , [ $this->date , $this->time , $this->level , $this->message ] ) ;
    }
}