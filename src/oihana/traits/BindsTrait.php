<?php

namespace oihana\traits;

use oihana\enums\Param;

/**
 *
 * @package oihana\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait BindsTrait
{
    /**
     * The default bind values definition of the model.
     * @var array|null
     */
    public ?array $binds = [] ;

    /**
     * Prepares the binding parameters to inject in a PDO statement.
     * @param array $init The binding parameters to push in the default binds associative array definition.
     * @return array
     */
    public function prepareBindVars( array $init = [] ) :array
    {
        return [ ...( $this->binds ?? [] ) , ...( $init[ Param::BINDS ] ?? [] ) ] ;
    }
}