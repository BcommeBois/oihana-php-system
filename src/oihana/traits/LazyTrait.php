<?php

namespace oihana\traits;

use DI\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Provides a configurable "lazy" mode for classes, e.g. to provision
 * missing resources (collections, views, tables, ...) at initialization time.
 *
 * The lazy flag can be resolved from three sources, in order of priority:
 *  1. The `lazy` entry of the DI container, when a container is available (checked by {@see LazyTrait::isLazy()}).
 *  2. The `lazy` key of an initialization array passed to {@see LazyTrait::initializeLazy()}.
 *  3. The `$lazy` property default value (`true`).
 *
 * Example usage:
 *
 * ```php
 * class MyModel
 * {
 *     use LazyTrait;
 * }
 *
 * $model = (new MyModel())->initializeLazy([ MyModel::LAZY => false ]);
 *
 * var_dump($model->isLazy()); // false
 *
 * // With a DI container, the container definition takes precedence
 * $container = new Container();
 * $container->set(MyModel::LAZY, true);
 *
 * $model->initializeLazy([], $container);
 *
 * var_dump($model->isLazy()); // true (from the container)
 * ```
 *
 * @package oihana\traits
 */
trait LazyTrait
{
    use ContainerTrait ;

    /**
     * The 'lazy' parameter constant.
     */
    public const string LAZY = 'lazy' ;

    /**
     * The default lazy mode (provision missing resources at init).
     * @var bool
     */
    public bool $lazy = true ;

    /**
     * Initialize the lazy flag.
     *
     * This method sets the `$lazy` property using the value provided in the `$init` array,
     * or falls back to the current `$lazy` value. Non-boolean init values are cast to boolean.
     * The optional `$container` reference is assigned to the `$container` property and
     * used later by {@see LazyTrait::isLazy()} to resolve the flag dynamically.
     *
     * @param array          $init      Optional initialization array, may contain a {@see LazyTrait::LAZY} key.
     * @param Container|null $container Optional DI container reference.
     * @return static                   Returns the current instance for chaining.
     */
    public function initializeLazy( array $init = [] , ?Container $container = null ) :static
    {
        if ( $container !== null )
        {
            $this->container = $container ;
        }
        $this->lazy = (bool) ( $init[ static::LAZY ] ?? $this->lazy ) ;
        return $this ;
    }

    /**
     * Check if lazy mode is active.
     *
     * If a DI container is assigned and defines a {@see LazyTrait::LAZY} entry,
     * that entry takes precedence; otherwise the `$lazy` property is returned.
     * The container value is cast to boolean.
     *
     * @return bool True if lazy mode is active, false otherwise.
     *
     * @throws ContainerExceptionInterface If an error occurs while retrieving the entry from the container.
     * @throws NotFoundExceptionInterface  If the entry is removed from the container between the check and the retrieval.
     */
    public function isLazy() :bool
    {
        if ( isset( $this->container ) && $this->container->has( static::LAZY ) )
        {
            return (bool) $this->container->get( static::LAZY ) ;
        }
        return $this->lazy ;
    }
}
