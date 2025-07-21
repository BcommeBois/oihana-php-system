<?php

namespace oihana\traits\alters;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\models\traits\DocumentsTrait;
use oihana\enums\Param;

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
                return $model->get( [ Param::BINDS => [ Param::ID => $value ] ] ) ; // Add the options like Param::CACHEABLE ...
            }
            return $value ;
        }
        return $value ;
    }
}