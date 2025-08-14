<?php

namespace oihana\init;

use DI\DependencyException;
use DI\NotFoundException;
use oihana\enums\Char;
use oihana\traits\ConfigTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Unit tests for setIniIfExists()
 *
 * Notes:
 * - We use the display_errors directive because it is safe to modify at runtime in CLI.
 * - Each test restores the original value in tearDown to avoid side effects.
 */
class ConfigTraitTest extends TestCase
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

        $this->initConfig($init);
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
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->method('has')
            ->willReturnCallback(fn($key) => $key === 'my_config');
        $mockContainer->method('get')
            ->willReturnCallback(fn($key) => $key === 'my_config' ? ['baz' => 'qux'] : null);

        $this->initConfig(['config' => 'my_config'], $mockContainer);
        $this->assertEquals(['baz' => 'qux'], $this->config);
    }

    public function testInitConfigFallbackToContainerConfigKey()
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->method('has')
            ->willReturnMap([[ self::CONFIG, true]]);
        $mockContainer->method('get')
            ->willReturnMap([[ self::CONFIG, ['a' => 1]]]);

        $this->initConfig([], $mockContainer);
        $this->assertEquals(['a' => 1], $this->config);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testInitConfigPathDirect()
    {
        $init = [
            self::CONFIG_PATH => '/path/to/config.php'
        ];

        $this->initConfigPath($init);
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
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->method('has')
            ->willReturnMap([['my_path', true]]);
        $mockContainer->method('get')
            ->willReturnMap([['my_path', '/etc/config.php']]);

        $this->initConfigPath(['configPath' => 'my_path'], $mockContainer);
        $this->assertEquals('/etc/config.php', $this->configPath);
    }

    public function testInitConfigPathFallbackToContainerKey()
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->method('has')
            ->willReturnMap([[ self::CONFIG_PATH, true]]);
        $mockContainer->method('get')
            ->willReturnMap([[ self::CONFIG_PATH, '/default/path.php']]);

        $this->initConfigPath([], $mockContainer);
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
        $this->initConfig();
        $this->initConfigPath();
        $this->assertEquals([], $this->config);
        $this->assertEquals(Char::EMPTY, $this->configPath);
    }
}
