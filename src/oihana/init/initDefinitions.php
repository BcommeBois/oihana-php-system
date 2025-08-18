<?php

namespace oihana\init;

use Exception ;

use oihana\files\enums\RecursiveFilePathsOption;
use function oihana\files\recursiveFilePaths;
use function oihana\files\requireAndMergeArrays;

/**
 * Initialize all DI container definitions by loading and merging PHP definition files.
 *
 * This function scans $basePath recursively for .php files, requires each file,
 * and merges the resulting arrays into a single definitions array. It is intended
 * to assemble service definitions for a DI container.
 *
 * Behavior:
 * - Only files with the "php" extension are considered.
 * - Files are discovered recursively in subdirectories.
 * - Each file must return an array; non-array returns may cause merge issues or exceptions in requireAndMergeArrays.
 *
 * @param string $basePath Absolute or relative path to the root directory containing definition files.
 * @return array Merged definitions array.
 * @throws Exception If a required file cannot be read/included or merging fails.
 *
 * @see recursiveFilePaths()
 * @see requireAndMergeArrays()
 * @see initContainer()
 *
 * @example
 * ```php
 * use DI\ContainerBuilder;
 * use function oihana\init\initDefinitions;
 *
 * // Load all container definitions from the 'definitions' directory
 * $definitions = initDefinitions(__DIR__ . '/../../definitions');
 *
 * // Example: pass the definitions to your container builder
 * $containerBuilder = new ContainerBuilder() ;
 *
 * $containerBuilder->addDefinitions( $definitions ) ;
 *
 * $containerBuilder->build() ;
 * ```
 */
function initDefinitions( string $basePath ) : array
{
    return requireAndMergeArrays( recursiveFilePaths( $basePath , [ RecursiveFilePathsOption::EXTENSIONS => [ 'php' ] ] ) ) ;
};
