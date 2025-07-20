<?php

namespace oihana\models\traits ;

use oihana\enums\Param;
use UnexpectedValueException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\enums\Char;

use oihana\models\interfaces\ListModel;

/**
 * @package oihana\models\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait ListModelTrait
{
    /**
     * The product sources reference.
     * @var ?ListModel
     */
    public ?ListModel $list ;

    /**
     * Asserts the existence of the 'list' property.
     * @return void
     * @throws UnexpectedValueException If the 'list' property is not set.
     */
    protected function assertListModel():void
    {
        if( !isset( $this->list ) )
        {
            throw new UnexpectedValueException( 'The list property is not set.' ) ;
        }
    }

    /**
     * Initialize the list model reference.
     * @param array $init
     * @param ContainerInterface|null $container
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function initializeListModel( array $init = [] , ?ContainerInterface $container = null ) :void
    {
        $list = $init[ Param::LIST ] ?? null ;
        if( is_string( $list ) && $list != Char::EMPTY && $container?->has( $list ) )
        {
            $list = $container->get( $list ) ;
        }
        $this->list = $list instanceof ListModel ? $list : null ;
    }
}