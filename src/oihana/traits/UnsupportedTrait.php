<?php

namespace oihana\traits;

use oihana\enums\Char;
use oihana\exceptions\UnsupportedOperationException;

/**
 * Trait UnsupportedTrait
 *
 * Provides a standardized way to signal that a specific method or operation is not supported
 * in the current class or context.
 *
 * This trait is useful for abstract base classes, partial implementations, or stubs,
 * where certain methods should intentionally throw an exception when invoked.
 *
 * Features:
 * - Throws a consistent `UnsupportedOperationException` when calling the `unsupported()` method.
 * - Allows optional method name labeling for clarity in the exception message.
 *
 * Dependencies:
 * - `oihana\enums\Char` for optional formatting (e.g., empty string).
 * - `oihana\exceptions\UnsupportedOperationException` to indicate the operation is disallowed.
 *
 * Example usage:
 * ```php
 * use oihana\traits\UnsupportedTrait;
 *
 * class ReadOnlyRepository {
 *     use UnsupportedTrait;
 *
 *     public function save($item)
 *     {
 *         $this->unsupported(__FUNCTION__); // Will throw exception
 *     }
 * }
 * ```
 */
trait UnsupportedTrait
{
    /**
     * Updates an item in the model.
     * @param string $method
     * @throws UnsupportedOperationException
     * @return void
     */
    protected function unsupported( string $method = Char::EMPTY ) : void
    {
        if( $method === Char::EMPTY )
        {
            throw new UnsupportedOperationException( __METHOD__ ) ;
        }

        throw new UnsupportedOperationException( __METHOD__ . Char::DOUBLE_COLON . $method ) ;
    }
}