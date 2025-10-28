<?php

namespace oihana\models\traits\signals;

use oihana\signals\Signal;

/**
 * Provides replace-related signals.
 *
 * This trait defines signals that are emitted **before** and **after** a document
 * is replaced, allowing observers to react to the replace event.
 *
 * Signals:
 * - `$beforeReplace`: Emitted before the replace occurs.
 * - `$afterReplace`: Emitted after the replace is complete.
 *
 * @author Marc Alcaraz (ekameleon)
 * @since 1.0.0
 * @package oihana\models\traits\signals
 */
trait HasReplaceSignals
{
    /**
     * Signal emitted after a document has been deleted.
     *
     * Observers connected to this signal receive the deleted document and optional context.
     *
     * @var Signal|null
     */
    public ?Signal $afterReplace = null ;

    /**
     * Signal emitted before a document is deleted.
     *
     * Observers connected to this signal receive the document that is about to be deleted.
     *
     * @var Signal|null
     */
    public ?Signal $beforeReplace = null ;

    /**
     * Initializes the replace signals.
     *
     * Creates new Signal instances for `$beforeReplace` and `$afterReplace`.
     *
     * @return static Returns `$this` for method chaining.
     *
     * @example
     * ```php
     * $document->initializeReplaceSignals()
     *          ->beforeReplace?->connect(fn($doc) => echo "About to replace {$doc->id}");
     * ```
     */
    public function initializeReplaceSignals():static
    {
        $this->afterReplace  = new Signal() ;
        $this->beforeReplace = new Signal() ;
        return $this ;
    }
}