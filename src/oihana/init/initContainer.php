<?php

namespace oihana\init;

use Exception ;
use DI\Definition\Source\DefinitionSource;
use DI\Container;
use DI\ContainerBuilder;

/**
 * Initialize and build a PHP-DI container for the application.
 *
 * This function creates a ContainerBuilder, adds one or more definition sources,
 * and returns the built Container. Definitions can be provided as:
 * - string: path to a PHP definition file (returning an array of definitions).
 * - array: an associative array of definitions.
 * - DefinitionSource: any PHP-DI compatible definition source.
 *
 * Notes:
 * - Later definition sources can override entries defined earlier.
 * - To load multiple files from a directory, see initDefinitions().
 *
 * @param string|array|DefinitionSource ...$definitions One or more definition sources (file path(s), array(s), or DefinitionSource instances).
 * @return Container The built dependency injection container.
 * @throws Exception If the container build process fails.
 *
 * @see initDefinitions()
 * @see ContainerBuilder
 * @link https://php-di.org/doc/definition.html PHP-DI definitions documentation
 *
 * @example
 * ```php
 * use DI\Container;
 * use function oihana\init\{initContainer, initDefinitions};
 *
 * // From a definitions directory (merges all PHP files returning arrays)
 * $definitions = initDefinitions(__DIR__ . '/../../definitions');
 *
 * // Add additional inline definitions that can override previous ones
 * $inline =
 * [
 *     'config.timezone' => 'UTC',
 * ];
 *
 * // Build the container with multiple sources
 * // @var Container $container
 * $container = initContainer($definitions, $inline, __DIR__ . '/extra-definitions.php');
 *
 * // Retrieve a service
 * // $logger = $container->get(App\Logger::class);
 * ```
 */
function initContainer( string|array|DefinitionSource ...$definitions ) :Container
{
    $containerBuilder = new ContainerBuilder() ;
    $containerBuilder->addDefinitions( ...$definitions ) ;
    return $containerBuilder->build() ;
};
