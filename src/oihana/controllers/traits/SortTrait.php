<?php

namespace oihana\controllers\traits ;

use oihana\controllers\enums\ControllerParam;

trait SortTrait
{
    public ?string $sortDefault = null ;

    /**
     * Initialize the sort behavior with an associative array definition.
     * @param array $init
     * @param string|null $defaultValue
     * @return static
     */
    protected function initializeSort( array $init = [] , ?string $defaultValue = null ) :static
    {
        $this->sortDefault = $init[ ControllerParam::SORT_DEFAULT ] ?? $this->sortDefault ?? $defaultValue ;
        return $this ;
    }
}