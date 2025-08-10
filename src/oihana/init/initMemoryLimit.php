<?php

namespace oihana\init;

use oihana\enums\IniOptions;
use oihana\reflections\exceptions\ConstantException;

/**
 * Initialize the default memory limit of the PHP application.
 *
 * This function sets the PHP memory_limit ini directive to the given value if provided,
 * otherwise it uses the provided default memory limit.
 *
 * @param string|null $memoryLimit The memory limit value to set (e.g. '256M', '1G', or '-1' for unlimited), or null to use default.
 * @param string $defaultMemoryLimit The default memory limit to use if $memoryLimit is null. Default is '128M'.
 *
 * @return bool True if the ini setting was successfully set, false otherwise.
 *
 * @throws ConstantException
 *
 * @example
 * ```php
 * // Sets memory_limit to 256M
 * initMemoryLimit('256M');
 *
 * // Uses the default value (128M)
 * initMemoryLimit(null);
 * ```
 */
function initMemoryLimit( ?string $memoryLimit , string $defaultMemoryLimit = "128M" ) : bool
{
    return setIniIfExists(IniOptions::MEMORY_LIMIT , $memoryLimit ?? $defaultMemoryLimit ) ;
}
