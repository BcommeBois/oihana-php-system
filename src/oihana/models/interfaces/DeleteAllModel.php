<?php

namespace oihana\models\interfaces;

/**
 * @package oihana\models\interfaces
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
interface DeleteAllModel
{
    /**
     * Delete a set of items in the model.
     * @param array $init
     * @return object|null
     */
    public function deleteAll( array $init = [] ) :mixed ;
}