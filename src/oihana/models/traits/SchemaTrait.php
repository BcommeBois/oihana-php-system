<?php

namespace oihana\models\traits;

use Closure;
use InvalidArgumentException;
use oihana\models\enums\ModelParam;

/**
 * Provides methods for initialize a 'schema' property.
 *
 * @package oihana\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait SchemaTrait
{
    /**
     * The internal schema to use to hydrate the resources.
     * @var null|string|Closure
     */
    public null|string|Closure $schema = null ;

    /**
     * Check if schema is defined (either as string or callable).
     * @return bool
     */
    public function hasSchema(): bool
    {
        return $this->schema !== null;
    }

    /**
     * Get the resolved schema value.
     * @return string|null
     */
    public function getSchema(): ?string
    {
        if ( is_callable( $this->schema ) )
        {
            return ($this->schema)() ;
        }
        return $this->schema ;
    }

    /**
     * Initialize the 'schema' property.
     * @param array $init
     * @return static
     */
    public function initializeSchema( array $init = [] ):static
    {
        $value = $init[ ModelParam::SCHEMA ] ?? null ;

        if ($value !== null && !is_string($value) && !( $value instanceof Closure ) )
        {
            throw new InvalidArgumentException('The `schema` property must be a string or Closure.') ;
        }

        $this->schema = $value ;

        return $this ;
    }
}