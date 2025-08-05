<?php

namespace oihana\interfaces;

/**
 * Interface for objects that can be converted to arrays.
 *
 * This interface defines a contract for objects that can provide an array representation of their data,
 * useful for serialization, data export, or configuration management.
 *
 * @package oihana\interfaces
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
interface Arrayable
{
    /**
     * Converts the object to an array.
     * @return array The array representation of the object.
     */
    public function toArray(): array;
}