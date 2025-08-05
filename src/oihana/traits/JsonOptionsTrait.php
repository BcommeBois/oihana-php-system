<?php

namespace oihana\traits;

trait JsonOptionsTrait
{
    /**
     * The json encode options value.
     * @var int
     */
    public int $jsonOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ;

    /**
     * The 'jsonOptions' parameter constant.
     */
    public const string JSON_OPTIONS = 'jsonOptions' ;

    /**
     * Initialize the documents reference.
     * @param array $init
     * @return static
     */
    protected function initializeJsonOptions( array $init = [] ) :static
    {
        $this->jsonOptions = $init[ static::JSON_OPTIONS ] ?? $this->jsonOptions ;
        return $this ;
    }
}