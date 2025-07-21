<?php

namespace oihana\traits\alters;

use oihana\enums\Char;

trait AlterUrlPropertyTrait
{
    /**
     * Generates a document url.
     * @param array|object $document
     * @param array $options
     * @param bool|null $isArray
     * @param bool $modified
     * @param string $propertyName The default property value to use to alter the url property (Default 'id').
     * @return string
     */
    public function alterUrlProperty( array|object $document ,  array $options = [] , ?bool $isArray = null , bool &$modified = false , string $propertyName = 'id' ): string
    {
        $path     = $options[0] ?? Char::EMPTY ;
        $name     = $options[1] ?? $propertyName  ;
        $modified = true ;
        return $path . ( $this->getKeyValue( $document , $name , $isArray ) ?? Char::EMPTY ) ;
    }
}