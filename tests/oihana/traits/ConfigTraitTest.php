<?php

namespace tests\oihana\traits;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use oihana\enums\Char;
use oihana\traits\ConfigTrait;

use PHPUnit\Framework\TestCase;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ConfigTraitTest extends TestCase
{
    use ConfigTrait;

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testInitConfigWithArray()
    {
        $init = [ self::CONFIG => ['foo' => 'bar'] ] ;
        $this->initializeConfig( $init );
        $this->assertEquals(['foo' => 'bar'], $this->config);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testInitConfigWithContainer()
    {
        $container = new Container() ;
        $container->set( 'my_config' ,  ['baz' => 'qux'] );
        $this->initializeConfig( [ self::CONFIG => 'my_config'], $container ) ;
        $this->assertEquals(['baz' => 'qux'], $this->config) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testInitConfigFallbackToContainerConfigKey()
    {
        $container = new Container() ;
        $container->set( self::CONFIG , ['a' => 1 ] );
        $this->initializeConfig( container: $container ) ;
        $this->assertEquals( ['a' => 1] , $this->config ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testInitConfigPathDirect()
    {
        $init = [ self::CONFIG_PATH => '/path/to/config.php' ] ;
        $this->initConfigPath( $init );
        $this->assertEquals('/path/to/config.php', $this->configPath);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testInitConfigPathWithContainer()
    {
        $container = new Container() ;
        $container->set( 'my_path' , '/etc/config.php' );
        $this->initConfigPath([ self::CONFIG_PATH => 'my_path'], $container );
        $this->assertEquals('/etc/config.php', $this->configPath);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testInitConfigPathFallbackToContainerKey()
    {
        $container = new Container() ;
        $container->set( self::CONFIG_PATH , '/default/path.php' );
        $this->initConfigPath( container: $container ) ;
        $this->assertEquals('/default/path.php', $this->configPath);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testDefaultsWhenNoConfigOrPath()
    {
        $this->initializeConfig();
        $this->initConfigPath();
        $this->assertEquals([], $this->config);
        $this->assertEquals(Char::EMPTY, $this->configPath);
    }
}
