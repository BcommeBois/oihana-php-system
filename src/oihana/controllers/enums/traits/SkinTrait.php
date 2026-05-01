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
     * Internal projection — exposes server-only fields that must NEVER
     * leak through the public HTTP surface (e.g. the SHA-256 of the
     * pending-email verification code on `User`).
     *
     * **Invariant — do NOT register `Skin::INTERNAL` in any controller's
     * `Arango::SKINS` list.** Doing so would expose the underlying fields
     * via `?skin=internal` on a public route. The controller's
     * {@see \oihana\controllers\traits\prepare\PrepareSkin::isValidSkin()}
     * filter rejects any skin not in that list and falls back to the
     * default — so as long as `INTERNAL` stays out of the list, no HTTP
     * caller can request it. This is the security guarantee.
     *
     * No matching Casbin permission exists, by design. Granting one
     * (e.g. `users:skin.internal`) would let a superadmin attribute it
     * to a user via `POST /users/{id}/permissions/{permKey}` and break
     * the invariant. If a future use-case really needs HTTP access to
     * an `internal`-projected document (admin debug tool, audit page),
     * introduce a dedicated permission AND a `Capability::PARAMS` gate
     * AND a hardcoded whitelist preventing the permission from being
     * attributed in the first place — all three layers, not just one.
     *
     * Server-side traits call `model->get([SKIN => INTERNAL])` directly.
     * The capability framework lives on the HTTP controller layer, not
     * on the model — direct model calls are therefore not gated, by
     * design. They remain trusted because they originate from server
     * PHP code.
     */
    public const string INTERNAL = 'internal' ;
}
