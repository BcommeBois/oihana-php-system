<?php

namespace tests\oihana\traits;

use DI\Container;

use DI\DependencyException;
use DI\NotFoundException;
use oihana\traits\CacheableTrait;
use PHPUnit\Framework\TestCase;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class MockCacheable
{
    use CacheableTrait;
}

final class CacheableTraitTest extends TestCase
{
    private object $object;
    private CacheInterface $mockCache;

    protected function setUp(): void
    {
        $this->object        = new MockCacheable();
        $this->mockCache     = $this->createStub(CacheInterface::class ) ; // PSR-16 Mock
        $this->object->cache = $this->mockCache ;
    }

    // public function testClearCacheCallsClear(): void
    // {
    //     $mock = $this->createStub(CacheInterface::class ) ;
    //     $mock->method('clear' ) ;
    //     $this->object->clearCache();
    // }

    /**
     * @throws InvalidArgumentException
     */
    public function testDeleteCacheCallsDelete(): void
    {
        $this->mockCache->method('delete')->with('foo');
        $this->object->deleteCache('foo');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetCacheReturnsValue(): void
    {
        $this->mockCache->method('get')->with('key')->willReturn('value');
        $this->assertSame('value', $this->object->getCache('key'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetCacheReturnsNullWhenCacheIsNull(): void
    {
        $this->object->cache = null;
        $this->assertNull($this->object->getCache('key'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testHasCacheReturnsTrue(): void
    {
        $this->mockCache->method('has')->with('key')->willReturn(true);
        $this->assertTrue($this->object->hasCache('key'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testHasCacheReturnsFalseForNullKey(): void
    {
        $this->assertFalse($this->object->hasCache(null));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testHasCacheReturnsFalseWhenCacheIsNull(): void
    {
        $this->object->cache = null;
        $this->assertFalse($this->object->hasCache('key'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSetCacheStoresValueWhenCacheable(): void
    {
        $this->mockCache->method('set')->with('foo', 'bar')->willReturn(true);
        $this->assertTrue($this->object->setCache('foo', 'bar'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSetCacheReturnsFalseWhenNotCacheable(): void
    {
        $this->object->cacheable = false;
        $this->mockCache->method('set');
        $this->assertFalse($this->object->setCache('foo', 'bar'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSetCacheMultipleStoresValues(): void
    {
        $this->mockCache->method('setMultiple')->with(['a' => 1, 'b' => 2], null)->willReturn(true);
        $this->assertTrue($this->object->setCacheMultiple(['a' => 1, 'b' => 2]));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSetCacheMultipleReturnsFalseWhenNotCacheable(): void
    {
        $this->object->cacheable = false;
        $this->mockCache->method('setMultiple');
        $this->assertFalse($this->object->setCacheMultiple(['x' => 1]));
    }

    public function testIsCacheableReturnsTrueWhenCacheSet(): void
    {
        $this->assertTrue($this->object->isCacheable());
    }

    public function testIsCacheableReturnsFalseWhenNoCache(): void
    {
        $this->object->cache = null;
        $this->assertFalse($this->object->isCacheable());
    }

    public function testIsCacheableOverridesFromInit(): void
    {
        $this->assertFalse ( $this->object->isCacheable( [ MockCacheable::CACHEABLE => false] ) ) ;
        $this->assertTrue  ( $this->object->isCacheable( [ MockCacheable::CACHEABLE => true ] ) ) ;
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testInitializeCacheAcceptsCacheInstance(): void
    {
        $result = $this->object->initializeCache( [ MockCacheable::CACHE => $this->mockCache ] );
        $this->assertSame($this->object, $result);
        $this->assertSame($this->mockCache, $this->object->cache);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testInitializeCacheWithStringAndContainerResolves(): void
    {
        $container = new Container();

        $container->set( 'cacheService', $this->mockCache );

        $result = $this->object->initializeCache( [MockCacheable::CACHE => 'cacheService'], $container );

        $this->assertSame($this->mockCache, $this->object->cache);
        $this->assertSame($this->object, $result);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testInitializeCacheWithInvalidValueSetsNull(): void
    {
        $result = $this->object->initializeCache( [ MockCacheable::CACHE => 'invalid' ] ) ;
        $this->assertNull($this->object->cache);
        $this->assertSame($this->object, $result);
    }

    public function testInitializeCacheableUsesInitArray(): void
    {
        $this->object->cacheable = false;
        $this->object->initializeCacheable( [ MockCacheable::CACHEABLE => true ] ) ;
        $this->assertTrue($this->object->cacheable);
    }

    public function testInitializeCacheableKeepsExistingValue(): void
    {
        $this->object->cacheable = true;
        $this->object->initializeCacheable();
        $this->assertTrue( $this->object->cacheable ) ;
    }
}
