<?php

namespace oihana\traits;

trait JsonOptionsTrait
{
    public const string JSON_OPTIONS = 'jsonOptions' ;

    /**
     * The json encode options value.
     * @var int
     */
    public int $jsonOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ;

    /**
     * Initialize the documents reference.
     * @param array $init
     * @return void
     */
    protected function initializeJsonOptions( array $init = [] ) :void
    {
        $this->jsonOptions = $init[ self::JSON_OPTIONS ] ?? $this->jsonOptions ;
    }
}