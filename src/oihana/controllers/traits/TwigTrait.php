<?php

namespace oihana\controllers\traits;

use oihana\controllers\enums\ControllerParam;
use oihana\controllers\enums\TwigParam;
use oihana\enums\Char;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * The Twig trait.
 */
trait TwigTrait
{
    /**
     * The default twig settings.
     * @var array
     */
    public array $twig = [] ;

    /**
     * The default twig settings definitions.
     */
    public const array TWIG_DEFAULT_SETTINGS =
    [
        TwigParam::BACKGROUND_COLOR => "#1f2937" ,
        TwigParam::PATTERN_COLOR    => "#1f2937" ,
        TwigParam::LOGO             => null,
        TwigParam::LOGO_DARK        => null,
        TwigParam::FULL_PATH        => Char::EMPTY,
    ];

    /**
     * The 'twig' key.
     */
    public const string TWIG = 'twig' ;

    /**
     * Returns the UI config definition to inject in a Twig view.
     * @param array $init
     * @return array
     */
    public function getUISetting( array $init = [] ) : array
    {
        return array_merge( $this->twig , $init ) ;
    }

    /**
     * Initialize the `twig` property.
     *
     * This method retrieves the default twig settings for the application,
     * either from the provided initialization array or from the dependency injection container.
     *
     * @param array                   $init      Optional initialization array (e.g., [ 'twig' => [ backgroundColor: '#ff0000' , ... ] ] ] ).
     * @param ContainerInterface|null $container Optional DI container for retrieving the App instance.
     *
     * @return static Returns the current controller instance for method chaining.
     *
     * @throws NotFoundExceptionInterface If the container is used and the App class is not found.
     * @throws ContainerExceptionInterface If the container throws an internal error.
     */
    public function initializeTwig( array $init = [] , ?ContainerInterface $container = null  ):static
    {
        $twig = $init[ self::TWIG ] ?? null ;

        if( $twig === null && $container instanceof ContainerInterface && $container->has( ControllerParam::PAGINATION ) )
        {
            $twig = $container->get( self::TWIG ) ;
        }

        $this->twig = array_merge( self::TWIG_DEFAULT_SETTINGS , is_array( $twig ) ? $twig : [] ) ;

        return $this ;
    }
}

