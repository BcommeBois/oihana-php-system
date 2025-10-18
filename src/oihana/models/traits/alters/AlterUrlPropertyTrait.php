<?php

namespace oihana\models\traits\alters;

use oihana\enums\Char;
use function oihana\core\accessors\getKeyValue;
use function oihana\files\path\joinPaths;

/**
 * Provides a method to generate a URL string from a document property.
 *
 * This trait depends on KeyValueTrait to safely read values from arrays or objects.
 *
 * Example usage:
 * ```php
 * use oihana\traits\alters\AlterUrlPropertyTrait;
 *
 * $document = ['id' => 42];
 * $url = $this->alterUrlProperty($document, ['/base/path']);
 * // returns '/base/path/42'
 * ```
 *
 * @package oihana\traits\alters
 * @since 1.0.0
 */
trait AlterUrlPropertyTrait
{
    /**
     * Generates a document URL using a property as the final path segment.
     *
     * @param array|object $document     Document to read the property from.
     * @param array        $options      Optional array of parameters :
     *                                       - [0] string Base path prefix
     *                                       - [1] string Property name to use (default 'id')
     * @param bool|null    $isArray      Optionally force document type (true=array, false=object)
     * @param bool         $modified     Reference variable updated if URL was altered
     * @param string       $propertyName Default property name if not specified in options (default 'id')
     *
     * @return string The generated URL
     */
    public function alterUrlProperty
    (
        array|object $document ,
        array        $options      = [] ,
        ?bool        $isArray      = null ,
        bool        &$modified     = false ,
        string       $propertyName = 'id'
    )
    :string
    {
        $modified = true ;
        $path     = $options[0] ?? Char::EMPTY ;
        $name     = $options[1] ?? $propertyName  ;
        return joinPaths( $path , getKeyValue( $document , $name , isArray: $isArray ) ?? Char::EMPTY ) ;
    }
}