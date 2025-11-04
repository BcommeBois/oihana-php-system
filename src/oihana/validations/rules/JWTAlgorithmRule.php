<?php

namespace oihana\validations\rules ;

use xyz\oihana\schema\constants\JWTAlgorithm;


/**
 * Rule: Validates that a given value is a supported JSON Web Token (JWT)
 * signing algorithm as defined in {@see JWTAlgorithm}.
 *
 * ---
 * **Usage**
 *
 * This rule ensures that the provided value matches one of the allowed
 * JWT signing algorithms, typically one of:
 *
 * ```
 * HS256, HS384, HS512, RS256, RS384, RS512, PS256, PS384, PS512, none
 * ```
 *
 * ---
 * **Examples**
 *
 * ```php
 * use oihana\validations\rules\JWTAlgorithmRule;
 * use Somnambulist\Components\Validation\Validator;
 *
 * $rule = new JWTAlgorithmRule() ;
 *
 * $validator = new Validator(
 *     ['alg' => 'HS256'],
 *     ['alg' => [ $rule ]]
 * );
 *
 * $validator->passes(); // true
 *
 * $validator = new Validator(
 *     ['alg' => 'MD5'],
 *     ['alg' => [$rule]]
 * );
 *
 * $validator->fails(); // true — "alg is not a valid JWT signing algorithm."
 * ```
 *
 * ---
 * **Customization**
 *
 * You can restrict validation to a subset of algorithms:
 *
 * ```php
 * $rule = new JWTAlgorithmRule(['HS256', 'RS256']);
 * $rule->check('RS512'); // false
 * ```
 *
 * @see JWTAlgorithm List of supported algorithms and their characteristics.
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz
 * @since   1.0.0
 */
class JWTAlgorithmRule extends ConstantsRule
{
    /**
     * Creates a new JWTAlgorithmRule instance.
     *
     * @param string[]|null $cases Optional list of allowed algorithms.
     * Defaults to all algorithms from {@see JWTAlgorithm::enums()}.
     *
     * @example
     * ```php
     * $rule = new JWTAlgorithmRule(['HS256', 'RS256']);
     * ``
     */
    public function __construct( ?array $cases = null )
    {
        parent::__construct(JWTAlgorithm::class , $cases ) ;
    }

    /**
     * The internal message pattern.
     * @var string
     */
    protected string $message = ":attribute is not a valid JWT signing algorithm.";
}