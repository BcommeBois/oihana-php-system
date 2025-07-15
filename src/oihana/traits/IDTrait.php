<?php

namespace oihana\traits;

/**
 * The command to manage an ArangoDB database.
 */
trait IDTrait
{
    public const string ID = 'id' ;

    /**
     * The unique identifier of the command.
     * @var null|int|string
     */
    public null|int|string $id = null ;

    /**
     * Initialize the unique identifier of the command.
     * @param array $init
     * @return void
     */
    public function initializeID( array $init = [] ):void
    {
        $this->id = $init[ self::ID ] ?? $this->id ;
    }
}