<?php

namespace oihana\controllers\enums\traits;

use oihana\reflect\traits\ConstantsTrait;

/**
 * The enumeration of all skins in the API.
 */
trait SkinTrait
{
    use ConstantsTrait ;

    public const string AUDIOS  = 'audios' ;
    public const string COMPACT = 'compact' ;
    public const string DEFAULT = 'default' ;
    public const string EXTEND  = 'extend' ;
    public const string FULL    = 'full' ;
    public const string LIST    = 'list' ;
    public const string MAIN    = 'main' ;
    public const string MAP     = 'map' ;
    public const string NORMAL  = 'normal' ;
    public const string PHOTOS  = 'photos' ;
    public const string VIDEOS  = 'videos' ;
}
