<?php

namespace oihana\controllers\helpers ;

use oihana\models\interfaces\DocumentsModel;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Resolves a DocumentsModel instance from a PSR-11 container or returns a default.
 *
 * This function attempts to retrieve a `DocumentsModel` instance based on the provided
 * definition. The definition can be:
 * - A `DocumentsModel` instance (returned directly),
 * - A string identifier for a model in a PSR-11 container.
 *
 * @param string|DocumentsModel|null $definition The definition, which can be: a DocumentsModel instance, a string identifier in the container, or null.
 * @param ContainerInterface|null    $container  Optional PSR-11 container used to resolve a string definition.
 * @param DocumentsModel|null        $default    Optional fallback model returned if none could be resolved.
 *
 * @return DocumentsModel|null The resolved `DocumentsModel` instance, the provided default, or `null` if none found.
 *
 * @throws ContainerExceptionInterface If an error occurs while retrieving the model from the container.
 * @throws NotFoundExceptionInterface  If a string definition is provided but not found in the container.
 */
function getDocumentModel
(
    string|null|DocumentsModel $definition = null ,
    ?ContainerInterface        $container  = null ,
    ?DocumentsModel            $default    = null
)
:?DocumentsModel
{
    if( $definition instanceof DocumentsModel )
    {
        return $definition ;
    }

    if( is_string( $definition ) && $container?->has( $definition ) )
    {
        $definition = $container->get( $definition ) ;
    }

    return $definition instanceof DocumentsModel ? $definition : $default ;
}