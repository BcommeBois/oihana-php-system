<?php

namespace oihana\controllers\traits ;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\controllers\enums\ControllerParam;

trait LanguagesTrait
{
    /**
     * The enumeration of all valid languages.
     * @var array
     */
    public array $languages = [] ;

    /**
     * This helper transform an array from client to prepare a i18n property. Ex: "[ 'fr' : 'bonjour' , 'en' : 'hello' ]"
     * @param array|null $field
     * @param bool $html
     * @return array|null
     */
    public function filterLanguages( ?array $field , bool $html = false ) :?array
    {
        if( is_array( $field ) && !empty( $field ) )
        {
            $items = [] ;
            if( count( $this->languages ) > 0 )
            {
                foreach( $this->languages as $lang )
                {
                    if( isset( $field[ $lang ] ) )
                    {
                        $items[$lang] = $html // if html remove all styles
                                      ? preg_replace('/(<[^>]+) style=".*?"/i', '$1', $field[$lang] )
                                      : $field[ $lang ] ;
                    }
                }
            }
            return $items ;
        }
        return null ;
    }

    /**
     * Initialize the internal $languages property.
     * @param array $init
     * @param ContainerInterface|null $container
     * @return static
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function initializeLanguages( array $init = [] , ?ContainerInterface $container ) :static
    {
        $languages = $init[ ControllerParam::LANGUAGES ] ?? null ;

        if( $languages == null && $container instanceof ContainerInterface && $container->has( ControllerParam::LANGUAGES ) )
        {
            $languages = $container->get( ControllerParam::LANGUAGES ) ;
        }

        $this->languages = is_array( $languages ) ? $languages : [] ;

        return $this ;
    }

    /**
     * @param array $texts
     * @param string|null $lang
     * @return array
     */
    public function translate( array $texts , ?string $lang = null ) :array
    {
        if( $lang === null )
        {
            return $texts ;
        }
        else
        {
            if( array_key_exists( $lang , $texts ) )
            {
                return $texts[ $lang ] ;
            }
            else if( array_key_exists( $this->languages[0] , $texts ) ) // TODO verify
            {
                return $texts[ $this->languages[0] ] ;
            }
        }

        return $texts ;
    }
}