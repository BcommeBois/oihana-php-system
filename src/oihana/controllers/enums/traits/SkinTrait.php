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

    /**
     * Internal projection — exposes server-only fields that must never
     * leak through the public HTTP surface (e.g. SHA-256 of the pending-email verification code on `User`).
     *
     * Aligned with the `?skin=offers.full` pattern : the skin value is
     * accepted by the controller only when the caller holds the matching
     * `<resource>:skin.internal` capability, gated through the
     * `ControllerParam::CAPABILITIES` block. NO role is granted this
     * capability by default — it exists so that server-side traits can
     * call `model->get([SKIN => INTERNAL])` (capabilities live on the
     * HTTP controller layer, not on the model layer) while remaining
     * unreachable from outside the API.
     */
    public const string INTERNAL = 'internal'    ;
}
