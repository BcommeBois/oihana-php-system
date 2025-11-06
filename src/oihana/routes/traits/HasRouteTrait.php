<?php

namespace oihana\routes\traits;

use oihana\routes\enums\RouteFlag;

trait HasRouteTrait
{
    /**
     * Bitmask representing enabled routes
     * @var int
     */
    public int $flags = RouteFlag::DEFAULT ;

    /**
     * The "flags" key parameter.
     */
    public const string FLAGS = 'flags' ;

    /**
     * Initialize the internal flags.
     * @param array|int $init
     * @return static
     */
    public function initializeFlags( array|int $init = [] ) :static
    {
        if ( is_int( $init ) )
        {
            $this->flags = $init ;
            return $this ;
        }

        if (isset( $init[ self::FLAGS ] ) && is_int($init[ self::FLAGS ] ) )
        {
            $this->flags = $init[ self::FLAGS ] ;
            return $this ;
        }

        $this->flags = RouteFlag::convertLegacyArray( $init ) ;

        return $this ;
    }

    /**
     * Check if COUNT route is enabled
     */
    public function hasCount(): bool
    {
        return RouteFlag::has( $this->flags , RouteFlag::COUNT ) ;
    }

    /**
     * Check if DELETE route is enabled
     */
    public function hasDelete(): bool
    {
        return RouteFlag::has( $this->flags , RouteFlag::DELETE ) ;
    }

    /**
     * Check if DELETE_MULTIPLE is enabled
     */
    public function hasDeleteMultiple(): bool
    {
        return RouteFlag::has( $this->flags , RouteFlag::DELETE_MULTIPLE ) ;
    }

    /**
     * Check if GET route is enabled
     */
    public function hasGet(): bool
    {
        return RouteFlag::has( $this->flags , RouteFlag::GET ) ;
    }

    /**
     * Check if LIST route is enabled
     */
    public function hasList(): bool
    {
        return RouteFlag::has( $this->flags , RouteFlag::LIST ) ;
    }

    /**
     * Check if PATCH route is enabled
     */
    public function hasPatch(): bool
    {
        return RouteFlag::has( $this->flags , RouteFlag::PATCH ) ;
    }

    /**
     * Check if POST route is enabled
     */
    public function hasPost(): bool
    {
        return RouteFlag::has( $this->flags , RouteFlag::POST ) ;
    }

    /**
     * Check if PUT route is enabled
     */
    public function hasPut(): bool
    {
        return RouteFlag::has( $this->flags , RouteFlag::PUT ) ;
    }

    /**
     * Get a human-readable description of enabled routes
     *
     * @return string Description of enabled routes
     */
    public function describeFlags(): string
    {
        return RouteFlag::describe( $this->flags ) ;
    }

    /**
     * Enable specific route flags
     *
     * @param int $flags Flags to enable
     * @return static
     */
    public function enableFlags( int $flags ) :static
    {
        $this->flags |= $flags ;
        return $this ;
    }

    /**
     * Disable specific route flags
     *
     * @param int $flags Flags to disable
     * @return static
     */
    public function disableFlags( int $flags ) :static
    {
        $this->flags &= ~$flags ;
        return $this ;
    }

    /**
     * Get current flags
     *
     * @return int Current flags bitmask
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * Set flags (replacing current flags)
     *
     * @param int $flags New flags value
     *
     * @return static
     */
    public function setFlags(int $flags): static
    {
        $this->flags = $flags;
        return $this;
    }
}