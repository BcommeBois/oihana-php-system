<?php

namespace oihana\validations\rules\http ;

use oihana\enums\http\HttpMethod;
use oihana\validations\rules\ConstantsRule;

/**
 * Rule: Validates that a given value is a supported HTTP method as defined in {@see HttpMethod}.
 *
 * **Usage**
 *
 * This rule ensures that the provided value matches one of the allowed HTTP methods,
 * typically one of:
 *
 * ```
 * GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS, TRACE, CONNECT, PURGE
 * ```
 *
 * **Examples**
 *
 * ```php
 * use oihana\validations\rules\http\HttpMethodRule;
 * use Somnambulist\Components\Validation\Validator;
 *
 * $rule = new HttpMethodRule();
 *
 * $validator = new Validator
 * (
 *     ['method' => 'GET'],
 *     ['method' => [$rule]]
 * );
 *
 * $validator->passes(); // true
 *
 * $validator = new Validator
 * (
 *     ['method' => 'FOO'],
 *     ['method' => [$rule]]
 * );
 *
 * $validator->fails(); // true — "method is not a valid HTTP method."
 * ```
 *
 * **Customization**
 *
 * You can restrict validation to a subset of HTTP methods:
 *
 * ```php
 * $rule = new HttpMethodRule(['GET', 'POST', 'DELETE']);
 * $rule->check('PATCH'); // false
 * ```
 *
 * @see HttpMethod List of supported HTTP methods.
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz
 * @since   1.0.0
 */
class HttpMethodRule extends ConstantsRule
{
    /**
     * Creates a new HttpMethodRule instance.
     *
     * @param string[]|null $cases Optional list of allowed algorithms.
     * Defaults to all algorithms from {@see HttpMethod::enums()}.
     *
     * @example
     * ```php
     * $rule = new HttpMethodRule(['GET','DELETE','POST','PATCH']);
     *
     * $rule->check('GET');
     * $validator->passes(); // true
     *
     * $rule->check('post');
     * $validator->fails(); // true
     * ``
     */
    public function __construct( ?array $cases = null )
    {
        parent::__construct(HttpMethod::class , $cases ) ;
    }

    /**
     * The internal message pattern.
     * @var string
     */
    protected string $message = ":attribute is not a valid HTTP method.";
}