<?php

namespace oihana\models\traits\alters;

use oihana\models\enums\ModelParam;
use org\schema\constants\Schema;

/**
 * Provides a default key to use in the alter methods.
 *
 * @package oihana\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait AlterKeyTrait
{
    /**
     * The default alter key reference.
     * @var string
     * @see AlterUrlPropertyTrait
     */
    public string $alterKey = Schema::ID ;

    /**
     * Initialize the 'alters' property.
     * @param array $init
     * @return static
     */
    public function initializeAlterKey( array $init = [] ):static
    {
        $this->alterKey = $init[ ModelParam::ALTER_KEY ] ?? Schema::ID ;
        return $this ;
    }
}