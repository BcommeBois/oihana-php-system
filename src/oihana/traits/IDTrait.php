<?php

namespace oihana\traits;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Initialize the unique identifier (id) of the object.
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
     * @param ContainerInterface|null $container
     * @return static
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function initializeID( array $init = [] , ?ContainerInterface $container = null ) :static
    {
        $id = $init[ static::ID ] ?? $this->id ;

        if( is_string( $id ) && isset( $container ) && $container->has( $id ) )
        {
            $id = $container->get( $id ) ;
        }

        $this->id = $id ?? $this->id ;

        return $this ;
    }
}