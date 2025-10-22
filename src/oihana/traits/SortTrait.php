<?php

namespace oihana\traits ;

trait SortTrait
{
    /**
     * The default sort definition.
     * @var string|null
     */
    public ?string $sortDefault = null ;

    /**
     * The 'sortDefault' parameter.
     */
    public const string SORT_DEFAULT = 'sortDefault' ;

    /**
     * Initialize the sort behavior with an associative array definition.
     * @param array $init
     * @param string|null $defaultValue
     * @return static
     */
    protected function initializeSort( array $init = [] , ?string $defaultValue = null ) :static
    {
        $this->sortDefault = $init[ self::SORT_DEFAULT ] ?? $this->sortDefault ?? $defaultValue ;
        return $this ;
    }
}