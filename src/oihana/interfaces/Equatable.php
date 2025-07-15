<?php

namespace oihana\interfaces;

/**
 * This interface is implemented by all equatable objects.
 */
interface Equatable
{
    /**
     * Compares the specified values for equality.
     * @param mixed $value The value to evaluates.
     * @return bool <code>true</code> if the the specified object is equal with this object.
     */
    public function equals( mixed $value ) :bool ;
}