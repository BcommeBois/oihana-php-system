<?php

namespace oihana\controllers\traits;

use DI\DependencyException;
use DI\NotFoundException;
use oihana\exceptions\http\Error404;
use oihana\exceptions\http\Error500;
use oihana\models\enums\ModelParam;
use oihana\models\interfaces\ExistModel;
use oihana\models\traits\DocumentsTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Provides utilities for validating "owner" arguments against specific Documents model references.
 *
 * This is mainly used to ensure that arguments passed to `get()`, `list()`, `count()` or `exist()` methods
 * actually correspond to existing document records.
 *
 * @package oihana\models\traits
 */
trait CheckOwnerArgumentsTrait
{
    use DocumentsTrait ;

    /**
     * The collection of all owner's arguments to check in the get|list|count|exist methods.
     */
    public ?array $owner = null ;

    /**
     * Check all the 'owner' arguments with a specific Documents model reference.
     * @param array $args
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws Error404
     * @throws Error500
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function checkOwnerArguments( array $args = [] ) :void
    {
        if( is_array( $this->owner ) && count( $this->owner ) > 0 )
        {
            foreach( $this->owner as $arg => $documents )
            {
                if( array_key_exists( $arg , $args ) )
                {
                    $documents = $this->getDocumentsModel( $documents ) ;
                    if( $documents instanceof ExistModel )
                    {
                        if( !$documents->exist( [ ModelParam::VALUE => $args[ $arg ] ] ) )
                        {
                            throw new Error404( sprintf( 'The %s argument is not found.' , $arg ) ) ;
                        }
                    }
                    else
                    {
                        throw new Error500
                        (
                            sprintf
                            (
                                "The %s argument can\'t be checked with a null or bad Documents model reference." ,
                                $arg
                            )
                        ) ;
                    }
                }
            }
        }
    }

    /**
     * Initialize the owner definition.
     * @param array $init
     * @return static
     */
    public function initializeOwner( array $init = [] ):static
    {
        $this->owner = $init[ ModelParam::OWNER ] ?? $this->owner ;
        return $this;
    }
}