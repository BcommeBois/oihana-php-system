<?php

namespace oihana\traits\alters;

use DI\DependencyException;
use DI\NotFoundException;

use oihana\models\enums\ModelParam;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\models\traits\DocumentsTrait;

/**
 * Provides logic to retrieve a document using a Documents model based on a given value and definition.
 * This trait depends on the `DocumentsTrait` to access the document model.
 *
 * The main method `alterGetDocument()` is typically used as part of a data transformation or hydration process
 * where a scalar or identifier is replaced by a fully loaded document instance.
 *
 * ### Usage example:
 *
 * ```php
 * class MyMapper {
 *     use AlterGetDocumentPropertyTrait;
 * }
 *
 * $mapper = new MyMapper();
 * $doc = $mapper->alterGetDocument(42, ['UserModel', 'id'], $modified);
 *
 * if ($modified) {
 *     echo "Document was loaded successfully.";
 * }
 * ```
 *
 * @package oihana\traits\alters
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait AlterGetDocumentPropertyTrait
{
    use DocumentsTrait ;

    /**
     * Gets a document with a Documents model.
     * @param mixed $value
     * @param array $definition
     * @param bool $modified
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function alterGetDocument( mixed $value , array $definition = [] , bool &$modified = false ): mixed
    {
        if( isset( $value ) )
        {
            $model = $this->getDocumentsModel( $definition[0] ?? null ) ;
            if( isset( $model ) )
            {
                $modified = true ;
                $key = $definition[1] ?? ModelParam::ID ;
                return $model->get( [ ModelParam::BINDS => [ $key => $value ] ] ) ; // Add the options like ModelParam::CACHEABLE ...
            }
            return $value ;
        }
        return $value ;
    }
}