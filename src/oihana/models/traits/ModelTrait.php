<?php

namespace oihana\models\traits;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\models\interfaces\DocumentsModel;

/**
 * @package oihana\models\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait ModelTrait
{
    use DocumentsTrait ;

    /**
     * The model reference.
     */
    public ?DocumentsModel $model = null ;

    /**
     * The 'model' parameter constant.
     */
    public const string MODEL = 'model' ;

    /**
     * Initialize the openEdge model.
     * @param array $init
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function initializeModel( array $init = [] ):void
    {
        $this->model = $this->getDocumentsModel( $init[ static::MODEL ] ?? $this->model ) ;
    }
}