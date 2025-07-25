<?php

namespace oihana\models\interfaces;

/**
 * @package oihana\models\interfaces
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
interface CountModel
{
    /**
     * Returns the number of items in the model.
     * @param array $init
     * @return int
     */
    public function count( array $init = [] ) :int ;
}