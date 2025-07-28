<?php

namespace oihana\logging;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Provides a trait for managing a LoggerManager instance.
 */
trait LoggerManagerTrait
{
    /**
     * The logger manager reference.
     * @var ?LoggerManager
     */
    public ?LoggerManager $manager ;

    /**
     * Initializes the LoggerManager instance.
     * @param LoggerManager|string|null $manager The LoggerManager instance, its service ID, or null.
     * @param ContainerInterface|null $container The DI Container reference.
     * @return static
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function initializeLoggerManager( null|LoggerManager|string $manager , ?ContainerInterface $container = null ) :static
    {
        if( is_string( $manager ) && isset( $container ) && $container->has( $manager ) )
        {
            $manager = $container->get( $manager ) ;
        }
        $this->manager = $manager instanceof LoggerManager ? $manager : null ;
        return $this ;
    }
}