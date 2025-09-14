<?php

namespace oihana\traits\alters;

/**
 * Provides a method to replace a value with a fixed new one if different.
 *
 * This trait is part of the alteration system and is intended to be used
 * in combination with {@see \oihana\traits\AlterDocumentTrait}.
 * It encapsulates the logic for the `Alter::VALUE` transformation type.
 *
 * Example usage:
 * ```php
 * use oihana\enums\Alter;
 * use oihana\traits\AlterDocumentTrait;
 * use oihana\traits\alters\AlterValueTrait;
 *
 * class Example
 * {
 *     use AlterDocumentTrait, AlterValueTrait;
 *
 *     public function __construct()
 *     {
 *         $this->alters = [
 *             'status' => [ Alter::VALUE, 'published' ],
 *         ];
 *     }
 * }
 *
 * $doc = [ 'status' => 'draft' ];
 *
 * $processor = new Example();
 * $result    = $processor->alter($doc);
 *
 * // Result:
 * // [
 * //     'status' => 'published'
 * // ]
 * ```
 *
 * @package oihana\traits\alters
 * @since   1.0.0
 */
trait AlterValueTrait
{
    /**
     * Replace a value with a new one if different, otherwise keep the original.
     *
     * @param mixed $value     The original value
     * @param mixed $newValue  The value to replace with
     * @param bool  $modified  Will be set to true if the value was replaced
     *
     * @return mixed The altered value
     */
    public function alterValue( mixed $value , mixed $newValue , bool &$modified = false ) : mixed
    {
        if( $value !== $newValue )
        {
            $modified = true ;
            return $newValue ;
        }
        return $value ;
    }
}