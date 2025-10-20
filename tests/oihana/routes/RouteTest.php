<?php

namespace tests\oihana\routes;

use PHPUnit\Framework\TestCase;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use Slim\App;

use Psr\Log\LoggerInterface;

use oihana\routes\Route;

class RouteTest extends TestCase
{
    protected Container       $container ;
    protected App             $app ;
    protected LoggerInterface $logger ;

    protected function setUp(): void
    {
        $this->app     = $this->createMock(App::class);
        $this->logger  = $this->createMock(LoggerInterface::class);
        $this->container = new Container() ;

        $this->container->set( App::class            , $this->app    ) ;
        $this->container->set( LoggerInterface::class, $this->logger ) ;
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testConstructorSetsDefaults(): void
    {
        $route = new Route($this->container);

        $this->assertSame(Route::DEFAULT_PREFIX, $route->prefix);
        $this->assertSame(Route::DEFAULT_OWNER_PLACEHOLDER, $route->ownerPlaceholder);
        $this->assertSame(Route::DEFAULT_ROUTE_PLACEHOLDER, $route->routePlaceholder);
        $this->assertTrue($route->verbose);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testConstructorAcceptsInitValues(): void
    {
        $route = new Route($this->container, [
            'name' => 'foo',
            'prefix' => 'bar',
            'suffix' => 'baz',
            'route' => '/api/test'
        ]);

        $this->assertSame('foo', $route->name);
        $this->assertSame('bar', $route->prefix);
        $this->assertSame('baz', $route->suffix);
        $this->assertSame('/api/test', $route->route);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testCleanParamsRemovesEmptyValues(): void
    {
        $route = new Route($this->container);

        $result = $route->cleanParams
        ([
            'a' => 'ok',
            'b' => '',
            'c' => null,
            'd' => 0,
        ]);

        $this->assertSame(['a' => 'ok', 'd' => 0], $result);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testDotifyReplacesSlashes(): void
    {
        $route = new Route($this->container);
        $this->assertSame('foo.bar', $route->dotify('foo/bar'));
        $this->assertSame('foobar', $route->dotify('foobar'));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testGetRouteAlwaysStartsWithSlash(): void
    {
        $route = new Route($this->container, ['route' => 'foo/bar']);
        $this->assertSame('/foo/bar', $route->getRoute());

        $route = new Route($this->container, ['route' => '/foo/bar']);
        $this->assertSame('/foo/bar', $route->getRoute());
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testGetNameCombinesPrefixAndSuffix(): void
    {
        $route = new Route( $this->container,
        [
            'prefix' => 'api',
            'name'   => 'user.get',
            'suffix' => 'json',
        ]);

        $this->assertSame('api.user.get.json', $route->getName());
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testGetNameDotifiesRouteWhenNoName(): void
    {
        $route = new Route($this->container, [
            'route' => 'foo/bar',
        ]);

        $this->assertSame('api.foo.bar', $route->getName());
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testCreateReturnsNullForInvalidInput(): void
    {
        $route = new Route($this->container);

        $this->assertNull($route->create(null));
        $this->assertNull($route->create(['not_associative']));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testCreateReturnsSameInstanceForRouteObject(): void
    {
        $route = new Route($this->container);
        $new = $route->create($route);

        $this->assertSame($route, $new);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testCreateReturnsNewRouteForDefinitionArray(): void
    {
        $route = new Route($this->container);
        $definition = [
            Route::CLAZZ => Route::class,
            Route::NAME => 'foo',
            Route::ROUTE => '/test'
        ];

        $newRoute = $route->create($definition);
        $this->assertInstanceOf(Route::class, $newRoute);
        $this->assertSame('foo', $newRoute->name);
        $this->assertSame('/test', $newRoute->route);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testExecuteCallsCallable(): void
    {
        $route = new Route($this->container);
        $called = false;
        $route->execute(function () use (&$called) { $called = true; });
        $this->assertTrue($called);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testExecuteCallsAllCallableInArray(): void
    {
        $route = new Route($this->container);
        $called = 0;
        $route->execute([
            function () use (&$called) { $called++; },
            function () use (&$called) { $called++; },
        ]);
        $this->assertSame(2, $called);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testInvokeDoesNothingIfNoRoutes(): void
    {
        $route = new Route($this->container, ['routes' => []]);
        $this->expectNotToPerformAssertions();
        $route();
    }
}