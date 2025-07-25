<?php

namespace oihana\models\interfaces;

/**
 * @package oihana\models\interfaces
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
interface UpsertModel
{
    /**
     * Upsert a document into the collection.
     * @param array $init
     * @return mixed
     */
    public function upsert( array $init = [] ) :mixed ;
}