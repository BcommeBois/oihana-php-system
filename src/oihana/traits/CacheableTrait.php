<?php

namespace oihana\traits;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Provides caching functionality through a PSR-16 compliant cache implementation.
 * This trait enables caching support for classes by defining methods for interacting
 * with a cache store as well as initialization of cache-related properties.
 */
trait CacheableTrait
{
    /**
     * The 'cache' parameter constant.
     */
    public const string CACHE = 'cache' ;

    /**
     * The 'cacheable' parameter constant.
     */
    public const string CACHEABLE = 'cacheable' ;

    /**
     * The PSR-16 cache reference.
     * @var CacheInterface|mixed|null
     */
    public ?CacheInterface $cache = null ;

    /**
     * Indicates if the instance use an internal PSR-16 c1ache.
     * @var bool
     */
    public bool $cacheable = true ;

    /**
     * Clear the cache.
     * @return void
     */
    public function clearCache():void
    {
        $this->cache?->clear() ;
    }

    /**
     * Delete a key/value in the cache.
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    public function deleteCache( string $key ):void
    {
        $this->cache?->delete( $key ) ;
    }

    /**
     * Returns the registered value in the cache with a specific key.
     * @param string $key
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getCache( string $key ):mixed
    {
        return $this->cache?->get( $key ) ?? null ;
    }

    /**
     * Indicates if the key is registered in the cache.
     * @param ?string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    public function hasCache( ?string $key ):bool
    {
        if( is_string( $key ) )
        {
            return $this->cache?->has( $key ) ?? false ;
        }
        return false ;
    }

    /**
     * Indicates if the ressource can use the cache.
     * @param array $init
     * @return bool
     */
    public function isCacheable( array $init = [] ):bool
    {
        return isset( $this->cache ) && ( $init[ self::CACHEABLE ] ?? $this->cacheable ?? false ) ;
    }

    /**
     * Set a key/value in the cache.
     * @param string $key
     * @param mixed $value
     * @return bool
     * @throws InvalidArgumentException
     */
    public function setCache( string $key , mixed $value ):bool
    {
        if( $this->cacheable )
        {
            return $this->cache?->set( $key , $value ) ?? false ;
        }
        return false ;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     * @param array $values
     * @param int|null $ttl
     * @return bool
     * @throws InvalidArgumentException
     */
    public function setCacheMultiple( array $values , ?int $ttl = null ):bool
    {
        if( $this->cacheable )
        {
            return $this->cache?->setMultiple( $values , $ttl ) ?? false ;
        }
        return false ;
    }

    /**
     * Initialize the cache reference.
     * @param array $init
     * @param Container|null $container
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function initializeCache(  array $init = [] , ?Container $container = null  ):void
    {
        $cache = $init[ self::CACHE ] ?? $this->cache ;

        if( is_string( $cache ) && isset( $container ) && $container->has( $cache ) )
        {
            $cache = $container->get( $cache ) ;
        }

        $this->cache = $cache instanceof CacheInterface ? $cache : null ;
    }

    /**
     * Initialize the cacheable property.
     * @param array $init
     * @return void
     */
    protected function initializeCacheable( array $init = [] ) :void
    {
        $this->cacheable = $init[ self::CACHEABLE ] ?? $this->cacheable ;
    }
}