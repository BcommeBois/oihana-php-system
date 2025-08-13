<?php

namespace oihana\traits;

use oihana\enums\Char;
use oihana\reflect\traits\ReflectionTrait;
use ReflectionException;

/**
 * Provides a basic string representation for an object by returning its class name
 * wrapped in square brackets or other defined characters.
 *
 * This trait uses reflection to dynamically obtain the short class name of the object.
 * It also caches the result for performance on repeated calls to `__toString()`.
 *
 * Methods:
 * - __toString(): Returns a string like "[ClassName]".
 *
 * Example usage:
 * ```php
 * use oihana\traits\ToStringTrait;
 *
 * class MyObject {
 *     use ToStringTrait;
 * }
 *
 * echo (new MyObject()); // Output: [MyObject]
 * ```
 */
trait ToStringTrait
{
    use ReflectionTrait ;

    /**
     * Returns a String representation of the object.
     * @return string A string representation of the object.
     * @throws ReflectionException
     */
    public function __toString():string
    {
        return Char::LEFT_BRACKET . $this->getShortName( $this ) . Char::RIGHT_BRACKET ;
    }
}