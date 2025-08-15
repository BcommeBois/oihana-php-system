<?php

namespace oihana\traits\alters;

/**
 * Casts a value (or all elements in an array) to integer.
 *
 * This method is typically used in property alteration pipelines
 * to ensure that the given value is strictly an integer type.
 * If the value is already an integer, it is returned unchanged and
 * the {@see $modified} flag remains `false`.
 *
 * - If the value is an array, each element is individually cast to integer.
 * - If the value is not an integer, it is cast using {@see intval()}.
 *
 * The `$modified` flag will be set to `true` whenever at least one
 * value is cast to integer (i.e., when its original type was not already `int`).
 *
 * Example:
 * ```php
 * use oihana\traits\alters\AlterIntPropertyTrait;
 *
 * class Product {
 *     use AlterIntPropertyTrait;
 * }
 *
 * $product = new Product();
 * $modified = false;
 *
 * // Casting a single value
 * $id = $product->alterIntProperty("42", $modified);
 * // $id === 42  (int)
 * // $modified === true
 *
 * // Casting an array of values
 * $values = $product->alterIntProperty(["10", "20", "30"], $modified);
 * // $values === [10, 20, 30] (all int)
 * // $modified === true
 *
 * // Already an int
 * $count = $product->alterIntProperty(5, $modified);
 * // $count === 5 (int)
 * // $modified === false
 * ```
 *
 * @param mixed $value    The value to cast. Can be a scalar or an array.
 * @param bool  $modified Reference flag set to `true` if any casting occurs.
 * @return array|int      The cast integer or array of integers.
 */
trait AlterIntPropertyTrait
{
    /**
     * Cast a value to integer.
     * If the value is an array, all elements in the array are casted.
     * ```
     * Property::PRICE => [ Alter::INT ] ,
     * ```
     * @param mixed $value
     * @param bool $modified
     * @return array|int
     */
    public function alterIntProperty( mixed $value , bool &$modified = false ): array|int
    {
        if ( is_array( $value ) )
        {
            $modified = true;
            return array_map('intval', $value);
        }

        if ( !is_int($value) )
        {
            $modified = true;
            return intval( $value ) ;
        }

        return $value ;
    }
}