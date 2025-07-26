<?php

namespace oihana\interfaces;

/**
 * This interface is implemented by all cloneable objects.
 *
 * @package oihana\interfaces
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
interface Cloneable
{
    /**
     * Creates a deep copy of the current instance.
     *
     * This method clones the current object and its properties.
     * Useful when you want to duplicate options without affecting
     * the original reference.
     *
     * @return static A new instance.
     */
    public function clone() :static ;
}