<?php

namespace oihana\validations\rules\models ;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use Somnambulist\Components\Validation\Exceptions\ParameterException;

/**
 * Rule: Validates that a given value is **unique** within a model managed by the DI container.
 *
 * This rule extends {@see ExistModelRule} and inverts its logic:
 * it passes only if the provided value **does not already exist** in the target model.
 *
 * The model must implement {@see \oihana\models\interfaces\ExistModel}, which defines
 * the `exist(array $criteria): bool` method used for lookups.
 *
 * ---
 *
 * ### **Usage**
 *
 * ```php
 * use oihana\validations\rules\UniqueModelRule;
 * use Somnambulist\Components\Validation\Validator;
 * use Psr\Container\ContainerInterface;
 *
 * // Assume $container provides access to models implementing ExistModel.
 *
 * $rule = new UniqueModelRule
 * (
 *     $container,
 *     [
 *         UniqueModelRule::MODEL => 'user.model',
 *         UniqueModelRule::KEY   => 'email',
 *     ]
 * );
 *
 * $validator = new Validator
 * (
 *     ['email' => 'john@example.com'],
 *     ['email' => [$rule]]
 * );
 *
 * $validator->passes(); // true if no existing user with this email
 * $validator->fails();  // true if a user with this email already exists
 * ```
 *
 * ---
 *
 * ### **Behavior**
 *
 * - Inherits all initialization logic from {@see ExistModelRule}.
 * - Calls {@see ExistModelRule::check()} internally and returns its logical negation.
 * - Requires that:
 *   - the model exists in the DI container,
 *   - the model implements {@see \oihana\models\interfaces\ExistModel},
 *   - the model correctly responds to `exist([ModelParam::KEY => ..., ModelParam::VALUE => ...])`.
 *
 * ---
 *
 * ### **Custom Error Messages**
 *
 * ```php
 * $rule = new UniqueModelRule($container, 'user.model', 'email');
 * $rule->message(':attribute already exists in :model.');
 * ```
 *
 * ---
 *
 * @see ExistModelRule The base class that checks for existence in a model.
 * @see \oihana\models\interfaces\ExistModel Interface that must be implemented by model classes.
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz
 * @since   1.0.0
 */
class UniqueModelRule extends ExistModelRule
{
    /**
     * The internal message pattern.
     * @var string
     */
    protected string $message = ":attribute must be unique in the model ':model', the value ':value' already exist.";

    /**
     * Checks whether the given value satisfies the condition.
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value satisfies the condition.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ParameterException
     */
    public function check( mixed $value ): bool
    {
        return !parent::check( $value ) ;
    }
}