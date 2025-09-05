<?php

namespace oihana\models\interfaces;

/**
 * List the documents in the model.
 * @package oihana\models\interfaces
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
interface ListModel
{
    /**
     * Returns a collection of items in the model.
     * @param array $init
     * @return array
     */
    public function list( array $init = [] ) :array ;
}