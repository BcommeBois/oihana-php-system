<?php

namespace oihana\controllers\traits ;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Views\Twig;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


/**
 * Provides seamless integration of the **Twig** templating engine into controllers using the **Slim Framework**.
 *
 * This trait offers:
 * - Automatic initialization of a `Twig` instance from a provided configuration array or via a **PSR-11** dependency injection container.
 * - A simplified `render()` method for rendering Twig templates into a PSR-7 `ResponseInterface`.
 * - Error handling through Twig's native exceptions for better debugging.
 *
 * Typical usage within a Slim controller:
 *
 * ```php
 * use oihana\controllers\traits\TwigTrait;
 *
 * class MyController {
 *     use TwigTrait;
 *
 *     public function __construct(ContainerInterface $container) {
 *         $this->initializeTwig([], $container);
 *     }
 *
 *     public function home($request, $response) {
 *         return $this->render($response, 'home.twig', [
 *             'title' => 'Welcome!',
 *             'user'  => 'Marc'
 *         ]);
 *     }
 * }
 * ```
 *
 * @package oihana\controllers\traits
 * @see     https://www.slimframework.com/docs/v4/features/templates.html
 * @see     https://twig.symfony.com/
 */
trait TwigTrait
{
    /**
     * The Twig view renderer instance.
     * @var Twig
     */
    public Twig $twig ;

    /**
     * The container key used to retrieve the Twig instance.
     * @var string
     */
    public const string TWIG = 'twig' ;

    /**
     * Initializes the Twig environment for rendering templates.
     *
     * This method first checks if a Twig instance is provided in the `$init` array.
     * If not, and a PSR-11 container is available, it attempts to fetch the Twig instance using the `self::TWIG` key.
     *
     * @param array                   $init      Optional initialization array (e.g., `['twig' => Twig $instance]`).
     * @param ContainerInterface|null $container Optional PSR-11 container for retrieving the Twig instance.
     *
     * @return static Returns the current instance for method chaining.
     *
     * @throws NotFoundExceptionInterface    If the container does not contain a Twig instance.
     * @throws ContainerExceptionInterface   If there is an error while retrieving Twig from the container.
     * @throws InvalidArgumentException      If no valid Twig instance is provided or available.
     */
    public function initializeTwig( array $init = [] , ?ContainerInterface $container = null ) :static
    {
        $twig = $init[ self::TWIG ] ?? null ;

        if( !$twig instanceof Twig && $container instanceof ContainerInterface && $container->has( self::TWIG ) )
        {
            $twig = $container->get( self::TWIG ) ;
        }

        if( !$twig instanceof Twig && $container instanceof ContainerInterface && $container->has( Twig::class ) )
        {
            $twig = $container->get( Twig::class ) ;
        }

        if ( !$twig instanceof Twig )
        {
            throw new InvalidArgumentException
            (
                sprintf
                (
                    "Invalid Twig instance. Expected an instance of '%s', got '%s'.",
                    Twig::class , is_object( $twig ) ? get_class( $twig ) : gettype( $twig )
                )
            );
        }

        $this->twig = $twig ;

        return $this ;
    }

    /**
     * Renders a Twig template into a PSR-7 response.
     *
     * @param ?Response $response The PSR-7 response instance.
     * @param string    $template The name of the Twig template to render.
     * @param array     $args     Optional array of parameters passed to the template.
     *
     * @return ?Response The modified response instance, or `null` if `$response` is not provided.
     *
     * @throws LoaderError  If the template cannot be found.
     * @throws RuntimeError If an error occurs during template rendering.
     * @throws SyntaxError  If the Twig template contains a syntax error.
     */
    public function render( ?Response $response , string $template , array $args = [] ) : ?Response
    {
        return isset( $response )
             ? $this->twig->render( $response , $template , $args )
             : null ;
    }
}