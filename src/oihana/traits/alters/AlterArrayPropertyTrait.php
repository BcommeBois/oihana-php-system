<?php

namespace oihana\traits\alters;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\enums\Alter;
use oihana\enums\Char;

trait AlterArrayPropertyTrait
{
    use AlterFloatPropertyTrait ,
        AlterIntPropertyTrait ;

    /**
     * Transform a string expression separated by semi-colon ';' to creates an array.
     * You can chain multiple alter definition to transform the content of the array, ex:
     * ```
     * Property::CATEGORY => [ Alter::ARRAY , Alter::CLEAN , Alter::JSON_PARSE ] ,
     * ```
     * The previous example transform the 'category' string in an Array and after remove all null or empty array elements and JSON parse all elements.
     * @param mixed $value
     * @param array $options
     * @param bool $modified
     * @return array
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function alterArrayProperty( mixed $value , array $options = [] , bool &$modified = false ):array
    {
        if( is_string( $value ) && $value != Char::EMPTY )
        {
            $value = explode( Char::SEMI_COLON , $value ) ;
        }
        $modified = true ;
        return is_array( $value ) ? $this->alterArrayElements( $value , $options ) : [] ;
    }

    /**
     * Alters all elements in an array.
     * @param array $array
     * @param array $options
     * @return array
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function alterArrayElements( array $array , array $options = [] ):array
    {
        if( count( $array )  > 0 && count( $options ) > 0 )
        {
            foreach( $options as $option )
            {
                if( is_array( $option ) )
                {
                    $type       = current( $option ) ;
                    $definition = array_slice( $option, 1 ) ;
                }
                else
                {
                    $type       = $option ;
                    $definition = [] ;
                }

                switch( $type )
                {
                    case Alter::CLEAN :
                    {
                        $array = array_filter( $array , fn( $item ) => $item != Char::EMPTY && isset($item)  ) ;
                        break ;
                    }
                    case Alter::FLOAT :
                    {
                        $array = $this->alterFloatProperty( $array ) ;
                        break ;
                    }

                    case Alter::GET :
                    {
                        $array = array_filter( $array , fn( $item ) => $this->alterGetDocument( $item , $definition ) ) ;
                        break ;
                    }

                    case Alter::INT :
                    {
                        $array = $this->alterIntProperty( $array ) ;
                        break ;
                    }
                    case Alter::JSON_PARSE :
                    {
                        $array = array_map( fn( $item ) => json_decode( $item ) ,  $array ) ;
                        break ;
                    }
                }
            }
        }
        return $array ;
    }
}