<?php

namespace oihana\validations\rules\auth ;

use oihana\validations\rules\ConstantsRule;
use xyz\oihana\schema\constants\Effect;

/**
 * Ensures a value is a valid Effect for Casbin/RBAC permissions.
 *
 * This rule validates that a given value is one of the supported Effects:
 * - `allow`
 * - `deny`
 *
 * **Usage Example**
 *
 * ```php
 * use oihana\validations\rules\JWTAlgorithmRule;
 * use Somnambulist\Components\Validation\Validator;
 *
 * $rule = new EffectRule() ;
 *
 * $validator = new Validator( ['effect' => 'deny'] , ['effect' => $rule] );
 * $validator->passes(); // true
 *
 * $validator = new Validator( ['effect' => 'unknown'] , ['effect' => $rule] );
 * $validator->fails(); // true — "effect is not a valid. Allowed values are 'allow' or 'deny'."
 * ```
 *
 * @see Effect The supported effects for Casbin permissions or RBAC rules.
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz
 * @since   1.0.0
 */
class EffectRule extends ConstantsRule
{
    /**
     * Creates a new EffectRule instance.
     *
     * @example
     * ```php
     * $rule = new EffectRule();
     *
     * $rule->check('allow') ; // valid
     * $rule->check('deny') ; // valid
     * $rule->check('unknown') ; // invalid
     * ``
     */
    public function __construct( )
    {
        parent::__construct(Effect::class ) ;
    }

    /**
     * The internal message pattern.
     * @var string
     */
    protected string $message = ":attribute is not a valid. Allowed values are 'allow' or 'deny'.";
}