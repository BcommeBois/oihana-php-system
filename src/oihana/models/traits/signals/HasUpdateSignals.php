<?php

namespace oihana\models\traits\signals;

use oihana\signals\Signal;

/**
 * Provides update-related signals.
 *
 * This trait defines signals that are emitted **before** and **after** a document
 * is updated, allowing observers to react to the update event.
 *
 * Signals:
 * - `$beforeUpdate`: Emitted before the update occurs.
 * - `$afterUpdate`: Emitted after the update is complete.
 *
 * @author Marc Alcaraz (ekameleon)
 * @since 1.0.0
 * @package oihana\models\traits\signals
 */
trait HasUpdateSignals
{
    /**
     * Signal emitted after a document has been deleted.
     *
     * Observers connected to this signal receive the deleted document and optional context.
     *
     * @var Signal|null
     */
    public ?Signal $afterUpdate = null ;

    /**
     * Signal emitted before a document is deleted.
     *
     * Observers connected to this signal receive the document that is about to be deleted.
     *
     * @var Signal|null
     */
    public ?Signal $beforeUpdate = null ;

    /**
     * Initializes the update signals.
     *
     * Creates new Signal instances for `$beforeUpdate` and `$afterUpdate`.
     *
     * @return static Returns `$this` for method chaining.
     *
     * @example
     * ```php
     * $document->initializeUpdateSignals()
     *          ->beforeUpdate?->connect(fn($doc) => echo "About to update {$doc->id}");
     * ```
     */
    public function initializeUpdateSignals():static
    {
        $this->afterUpdate  = new Signal() ;
        $this->beforeUpdate = new Signal() ;
        return $this ;
    }
}