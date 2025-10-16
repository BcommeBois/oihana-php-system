<?php

namespace oihana\routes\traits;

use oihana\routes\enums\RouteFlag;

trait HasRouteTrait
{
    public bool $hasCount          ;
    public bool $hasDelete         ;
    public bool $hasDeleteAll      ;
    public bool $hasDeleteMultiple ;
    public bool $hasGet            ;
    public bool $hasList           ;
    public bool $hasPatch          ;
    public bool $hasPost           ;
    public bool $hasPut            ;

    /**
     * Initialize the internal flags.
     * @param array $init
     * @return void
     */
    protected function initializeFlags( array $init = [] ) :void
    {
        $flag = ( $init[ RouteFlag::DEFAULT_FLAG ] ?? true ) === true ;
        $this->hasCount          = $init[ RouteFlag::HAS_COUNT           ] ?? $flag ;
        $this->hasDelete         = $init[ RouteFlag::HAS_DELETE          ] ?? $flag ;
        $this->hasDeleteAll      = $init[ RouteFlag::HAS_DELETE_ALL      ] ?? $flag ;
        $this->hasDeleteMultiple = $init[ RouteFlag::HAS_DELETE_MULTIPLE ] ?? $flag ;
        $this->hasGet            = $init[ RouteFlag::HAS_GET             ] ?? $flag ;
        $this->hasList           = $init[ RouteFlag::HAS_LIST            ] ?? $flag ;
        $this->hasPatch          = $init[ RouteFlag::HAS_PATCH           ] ?? $flag ;
        $this->hasPost           = $init[ RouteFlag::HAS_POST            ] ?? $flag ;
        $this->hasPut            = $init[ RouteFlag::HAS_PUT             ] ?? $flag ;
    }
}