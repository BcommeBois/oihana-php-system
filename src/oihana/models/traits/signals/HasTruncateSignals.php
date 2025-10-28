<?php

namespace oihana\models\traits\signals;

use oihana\signals\Signal;

/**
 * Provides truncated signals.
 *
 * This trait defines signals that are emitted **before** and **after** a collection
 * is truncated, allowing observers to react to the update event.
 *
 * Signals:
 * - `$beforeTruncate`: Emitted before the update occurs.
 * - `$afterTruncate`: Emitted after the update is complete.
 *
 * @author Marc Alcaraz (ekameleon)
 * @since 1.0.0
 * @package oihana\models\traits\signals
 */
trait HasTruncateSignals
{
    /**
     * Signal emitted after a collection has been truncated.
     *
     * Observers connected to this signal receive the deleted document and optional context.
     *
     * @var Signal|null
     */
    public ?Signal $afterTruncate = null ;

    /**
     * Signal emitted before a colection is truncated.
     *
     * Observers connected to this signal receive the document that is about to be deleted.
     *
     * @var Signal|null
     */
    public ?Signal $beforeTruncate = null ;

    /**
     * Initializes the truncate signals.
     *
     * Creates new Signal instances for `$beforeTruncate` and `$afterTruncate`.
     *
     * @return static Returns `$this` for method chaining.
     *
     * @example
     * ```php
     * $document->initializeTruncateSignals()
     *          ->beforeTruncate?->connect(fn($doc) => echo "About to truncate the collection");
     * ```
     */
    public function initializeTruncateSignals():static
    {
        $this->afterTruncate  = new Signal() ;
        $this->beforeTruncate = new Signal() ;
        return $this ;
    }
}