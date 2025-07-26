<?php

namespace oihana\traits;

/**
 * The command to manage an ArangoDB database.
 */
trait IDTrait
{
    /**
     * The unique identifier of the command.
     * @var null|int|string
     */
    public null|int|string $id = null ;

    /**
     * The 'id' parameter.
     */
    public const string ID = 'id' ;

    /**
     * Initialize the unique identifier of the command.
     * @param array $init
     * @return void
     */
    public function initializeID( array $init = [] ):void
    {
        $this->id = $init[ static::ID ] ?? $this->id ;
    }
}