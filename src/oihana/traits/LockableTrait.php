<?php

namespace oihana\traits;

use oihana\enums\Param;

/**
 * Provides functionality for managing a lockable state within a class.
 * This trait defines a constant and uses a flag to determine whether an instance can be locked.
 */
trait LockableTrait
{
    /**
     * The lockable flag to indicates if the instance is lockable or not.
     */
    public bool $lockable = false ;

    /**
     * Initialize the lockable flag.
     * @param array $init
     * @return bool
     */
    protected function initLockable( array $init = [] ) :bool
    {
        $lockable = $init[ Param::LOCKABLE ] ?? $this->lockable ?? false ;
        return is_bool( $lockable ) ? $lockable : false ;
    }
}