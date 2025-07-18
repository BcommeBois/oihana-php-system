<?php

namespace oihana\traits;

trait BindsTrait
{
    public const string BINDS = 'binds' ;

    /**
     * The default bind values definition of the model.
     * @var array|null
     */
    protected ?array $binds = [] ;

    /**
     * Prepares the binding parameters to inject in a PDO statement.
     * @param array $init The binding parameters to push in the default binds associative array definition.
     * @return array
     */
    public function prepareBindVars( array $init = [] ) :array
    {
        return [ ...( $this->binds ?? [] ) , ...( $init[ self::BINDS ] ?? [] ) ] ;
    }
}