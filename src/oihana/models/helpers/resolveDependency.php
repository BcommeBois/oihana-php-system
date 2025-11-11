<?php

namespace oihana\controllers\helpers ;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Resolves a dependency definition from a PSR-11 container or returns a default value.
 *
 * This function attempts to retrieve a service or object from a PSR-11 container
 * if a string identifier is provided. If the container is null, the dependency is
 * not found, or the input is not a string, the provided default value is returned.
 *
 * @param string|null             $dependency The container entry ID to resolve.
 * @param ContainerInterface|null $container  Optional PSR-11 container to resolve the dependency from.
 * @param mixed                   $default    Value to return if the dependency cannot be resolved.
 *
 * @return mixed Returns the resolved dependency from the container, or `$default` if not found.
 *
 * @throws ContainerExceptionInterface If an error occurs while retrieving the dependency from the container.
 * @throws NotFoundExceptionInterface  If a string definition is provided but not found in the container.
 *
 * @example
 * ```php
 * $logger = resolveDependency(LoggerInterface::class, $container, new NullLogger());
 * ```
 *
 * @package oihana\models\helpers
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
function resolveDependency
(
    ?string             $dependency ,
    ?ContainerInterface $container  = null ,
    mixed               $default    = null
)
:mixed
{
    if( !empty( $dependency ) && $container?->has( $dependency ) )
    {
        return $container->get( $dependency ) ;
    }

    return $default ;
}