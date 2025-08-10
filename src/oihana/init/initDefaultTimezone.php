<?php

namespace oihana\init;

/**
 * Initialize the application's default timezone.
 *
 * This function sets the global timezone used by all date/time functions.
 * If $timezoneId is null, $defaultTimezone will be applied.
 *
 * Notes:
 * - If an invalid identifier is provided, PHP will return false and emit a warning.
 * - Prefer passing null rather than an empty string to trigger the default timezone.
 *
 * @param ?string $timezoneId      Timezone identifier to apply. Use null to apply $defaultTimezone.
 * @param string  $defaultTimezone Timezone to use when $timezoneId is null (default 'Europe/Paris').
 * @return void
 *
 * @see date_default_timezone_set()
 * @link https://www.php.net/manual/en/timezones.php List of available timezones
 *
 * @example
 * ```php
 * use function oihana\init\initDefaultTimezone;
 *
 * // Use explicit timezone
 * initDefaultTimezone('UTC');
 *
 * // Fallback to provided default when first argument is null
 * initDefaultTimezone(null, 'Europe/Paris');
 *
 * // Typically, you might pick from configuration
 * $config = ['app' => ['timezone' => 'Europe/Lisbon']];
 * initDefaultTimezone($config['app']['timezone'] ?? null, 'UTC');
 * ```
 */
function initDefaultTimezone( ?string $timezoneId , string $defaultTimezone = 'Europe/Paris' ) :void
{
    date_default_timezone_set($timezoneId ?? $defaultTimezone ) ;
}
