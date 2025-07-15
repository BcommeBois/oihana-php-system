<?php

namespace oihana\traits;

use oihana\logging\LoggerTrait;

/**
 * Provides debugging and mock-related functionality.
 */
trait DebugTrait
{
    use LoggerTrait ;

    /**
     * Indicates if use the debug mode.
     * @var bool
     */
    public bool $debug = false ;

    /**
     * The mock flag to test the model.
     * @var bool
     */
    public bool $mock = false ;

    /**
     * The 'mock' parameter constant.
     */
    public const string MOCK = 'mock' ;

    /**
     * Initialize the mock flag.
     * @param array $init
     * @return bool
     */
    public function initializeMock( array $init = [] ):bool
    {
        return $init[ self::MOCK ] ?? $this->mock ;
    }

    /**
     * Indicates if the document use the mock mode.
     * @param array $init
     * @return bool
     */
    public function isMock( array $init = [] ) :bool
    {
        return $this->debug && ( $init[ self::MOCK ] ?? $this->mock ) ;
    }
}