<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Class Boolean
 *
 * A simple enumeration of string representations for boolean values: `"true"` and `"false"`.
 *
 * This class is useful when working with systems that expect boolean values
 * in string format (e.g. JSON, configuration files, XML, CLI flags, or external APIs).
 *
 * By providing symbolic constants instead of hardcoded strings, it improves
 * code clarity, consistency, and reduces the risk of typos.
 *
 * ### Example:
 * ```php
 * use oihana\enums\Boolean;
 *
 * $enabled = Boolean::TRUE;
 * $disabled = Boolean::FALSE;
 *
 * echo $enabled;  // Outputs: true
 * ```
 *
 * ### Features:
 * - Uses `ConstantsTrait` to support reflection and dynamic access to constants.
 * - Promotes semantic clarity in systems that serialize booleans as strings.
 *
 * @package oihana\enums
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class Boolean
{
    use ConstantsTrait ;

    public const string FALSE = 'false' ;
    public const string TRUE  = 'true'  ;
}


