<?php

namespace oihana\controllers\traits ;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Http\Message\ResponseInterface as Response;

use Slim\Views\Twig;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

trait RenderTrait
{
    /**
     * Render the specific view with the current template engine.
     * @param ?Response $response
     * @param string $template
     * @param array $args
     * @return ?Response
     * @throws DependencyException
     * @throws NotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render( ?Response $response , string $template , array $args = [] ) : ?Response
    {
        return isset( $response ) ? $this->container->get( Twig::class )->render( $response , $template , $args ) : null ;
    }
}