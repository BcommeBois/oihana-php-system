<?php

namespace oihana\models\traits;

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
     * @var string|null
     */
    public ?string $schema = null ;

    /**
     * Initialize the 'schema' property.
     * @param array $init
     * @return static
     */
    public function initializeSchema( array $init = [] ):static
    {
        $this->schema = $init[ ModelParam::SCHEMA ] ?? null ;
        return $this ;
    }
}