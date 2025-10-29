<?php

namespace tests\oihana\models\helpers;

use DI\Container;

use oihana\models\enums\ModelParam;
use oihana\models\Model;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function oihana\controllers\helpers\getModel;
use function oihana\controllers\helpers\resolveDependency;

class ResolveDependencyTest extends TestCase
{
    private Container $container ;

    protected function setUp(): void
    {
        $this->container = new Container() ;
        $this->container->set( 'string' , 'hello world' ) ;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testReturnsDefaultWhenDependencyIsNull(): void
    {
        $default = 'default-value';
        $result = resolveDependency(null, null, $default);
        $this->assertSame($default, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testReturnsDefaultWhenContainerIsNull(): void
    {
        $default = 123;
        $result = resolveDependency('some_service', null, $default);
        $this->assertSame($default, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testReturnsDefaultWhenContainerDoesNotHaveDependency(): void
    {
        $default = ['foo' => 'bar'];
        $result = resolveDependency('missing_service', $this->container , $default);
        $this->assertSame($default, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testReturnsResolvedDependencyFromContainer(): void
    {
        $result = resolveDependency('string', $this->container );
        $this->assertSame( 'hello world' , $result);
    }
}