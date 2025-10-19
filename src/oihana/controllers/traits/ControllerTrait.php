<?php

namespace oihana\controllers\traits;

use Exception;
use oihana\controllers\Controller;
use oihana\controllers\enums\ControllerParam;
use oihana\traits\ContainerTrait;

// FIXME : replace this method and use the 'oihana/controllers/helper/getController' function

trait ControllerTrait
{
    use ContainerTrait ;
    
    /**
     * Returns a Controller reference with a specific definition.
     * @param array|string|null|Controller $definition
     * <li>If the $definition is an array, try to use the $definition[ AQL::CONTROLLER ] reference to find the controller in the DI container.</li>
     * <li>If the $definition is a string, the $definition is the reference to find the controller in the DI container.</li>
     * <li>If the $definition is an instance of Controller the definition is returned directly.</li>
     * <li>Else return null.</li>
     * @param string|null $debugMethod
     * @return ?Controller
     */
    public function getController
    (
        array|string|null|Controller $definition ,
        string|null                  $debugMethod = null
    )
    :?Controller
    {
        try
        {
            if( is_array( $definition ) )
            {
                $definition = $definition[ ControllerParam::CONTROLLER ] ?? null ;
            }

            if( is_string( $definition ) && $this->container->has( $definition ) )
            {
                $definition = $this->container->get( $definition ) ;
                if( !( $definition instanceof Controller ) )
                {
                    throw new Exception( __METHOD__ . ' failed, the reference is not a Controller instance.' ) ;
                }
            }
            else
            {
                throw new Exception( __METHOD__ . ', failed, the controller reference doesn\'t exist on the DI container with the id: ' . $definition ) ;
            }

        }
        catch ( Exception $e )
        {
            $this->logger->warning( ( $debugMethod ?? __METHOD__ ) . ' failed, ' . $e->getMessage() ) ;
        }
        return $definition instanceof Controller ? $definition : null ;
    }
}