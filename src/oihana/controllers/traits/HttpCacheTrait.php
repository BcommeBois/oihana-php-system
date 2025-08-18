<?php

namespace oihana\controllers\traits;

use RuntimeException;

use DI\DependencyException;
use DI\NotFoundException;

use oihana\controllers\enums\ControllerParam;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

use Slim\HttpCache\CacheProvider;

/**
 * Trait providing helpers to manage HTTP caching in controllers.
 *
 * This trait allows controllers to:
 *   - Initialize an internal HTTP cache provider (`Slim\HttpCache\CacheProvider`).
 *   - Enforce cache control headers (deny cache, set ETag, Last-Modified).
 *
 * Usage:
 *   1. Initialize the HTTP cache provider via `initializeHttpCache()`.
 *   2. Use helper methods to modify PSR-7 response headers for caching.
 */
trait HttpCacheTrait
{
    /**
     * The cache provider reference.
     * This property holds the `Slim\HttpCache\CacheProvider` instance
     * used to modify HTTP response headers for caching.
     *
     * @var CacheProvider
     */
    protected CacheProvider $httpCache ;

    /**
     * Enforce the removal of browser cache for a response.
     *
     * Equivalent to setting headers like `Cache-Control: no-store, no-cache, must-revalidate`.
     *
     * @param ResponseInterface $response A PSR-7 response object
     * @return ResponseInterface A new response object with cache denial headers
     */
    public function denyCache( ResponseInterface $response ) : ResponseInterface
    {
        return $this->httpCache->denyCache( $response ) ;
    }

    /**
     * Initialize the internal HTTP cache provider.
     *
     * This method sets up the `$httpCache` property from:
     *   - The `$init` array, using the key `ControllerParam::HTTP_CACHE`
     *   - The DI container, if available and containing `Slim\HttpCache\CacheProvider`
     *
     * @param array $init Optional initialization array
     * @param ContainerInterface|null $container Optional DI container to retrieve the cache provider
     * @return static Returns the current instance for method chaining
     * @throws DependencyException If container dependency cannot be resolved
     * @throws NotFoundException If container entry is not found
     * @throws RuntimeException If no valid `CacheProvider` could be assigned
     */
    public function initializeHttpCache( array $init = [] , ?ContainerInterface $container = null ):static
    {
        $httpCache = $init[ ControllerParam::HTTP_CACHE ] ?? null;

        if( $httpCache === null && $container instanceof ContainerInterface && $container->has( CacheProvider::class ) )
        {
            $httpCache = $this->container->get( CacheProvider::class  ) ;
        }

        if( !$httpCache instanceof CacheProvider )
        {
            throw new RuntimeException( 'The controller `httpCache` property must be defined.' ) ;
        }

        $this->httpCache = $httpCache ;

        return $this ;
    }

    /**
     * Add an `ETag` header to a PSR-7 response object.
     *
     * The `ETag` is used by browsers and proxies for cache validation.
     *
     * @param ResponseInterface $response A PSR-7 response object
     * @param string $value The ETag value
     * @param string $type The ETag type: either `"strong"` or `"weak"`
     * @return ResponseInterface A new response object with the `ETag` header set
     */
    public function withEtag( ResponseInterface $response, string $value, string $type = 'strong' ): ResponseInterface
    {
        return $this->httpCache->withEtag( $response , $value , $type ) ;
    }

    /**
     * Add a `Last-Modified` header to a PSR-7 response object.
     *
     * This header informs caches of the last modification date of the resource.
     *
     * @param ResponseInterface $response A PSR-7 response object
     * @param int|string $time A UNIX timestamp or a string compatible with `strtotime()`
     * @return ResponseInterface A new response object with the `Last-Modified` header set
     */
    public function withLastModified( ResponseInterface $response , int|string $time ) : ResponseInterface
    {
        return $this->httpCache->withLastModified( $response , $time ) ;
    }
}