<?php

namespace oihana\controllers\traits;

use oihana\controllers\enums\TwigParam;
use oihana\enums\Char;

/**
 * The Twig trait.
 */
trait TwigTrait
{
    public static string $DEFAULT_BACKGROUND_COLOR = "#1f2937" ;
    public static string $DEFAULT_PATTERN_COLOR    = "#1f2937" ;

    /**
     * Returns the UI config definition to inject in a Twig view.
     * @param array $settings
     * @param array $init
     * @return array
     */
    protected function getUISetting( array $settings = [] , array $init = [] ) : array
    {
        return
        [
            TwigParam::BACKGROUND_COLOR => $init[ TwigParam::BACKGROUND_COLOR ] ?? static::$DEFAULT_BACKGROUND_COLOR ,
            TwigParam::PATTERN_COLOR    => $init[ TwigParam::PATTERN_COLOR    ] ?? static::$DEFAULT_PATTERN_COLOR    ,
            TwigParam::LOGO             => $init[ TwigParam::LOGO             ] ?? null ,
            TwigParam::LOGO_DARK        => $init[ TwigParam::LOGO_DARK        ] ?? null ,
            TwigParam::FULL_PATH        => $init[ TwigParam::FULL_PATH        ] ?? Char::EMPTY ,
            ...$settings
        ];
    }
}

