<?php

namespace tests\oihana\init;

use Devium\Toml\TomlError;

use PHPUnit\Framework\TestCase;

use function oihana\init\initConfig;

class InitConfigTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/oihana-init-config-' . uniqid() ;
        mkdir( $this->dir , 0o775 , true ) ;
        file_put_contents( $this->dir . '/config.toml' , "[app]\nname = \"system\"\n" ) ;
    }

    protected function tearDown(): void
    {
        @unlink( $this->dir . '/config.toml' ) ;
        @rmdir( $this->dir ) ;
    }

    /**
     * @throws TomlError
     */
    public function testInitConfigLoadsTomlFile()
    {
        $config = initConfig( $this->dir ) ;
        $this->assertSame([ 'app' => [ 'name' => 'system' ] ] , $config ) ;
    }

    /**
     * @throws TomlError
     */
    public function testInitConfigReturnsEmptyArrayWhenFileIsMissing()
    {
        $config = initConfig( $this->dir , 'missing.toml' ) ;
        $this->assertSame( [] , $config ) ;
    }

    /**
     * @throws TomlError
     */
    public function testInitConfigAppliesInitCallable()
    {
        $config = initConfig( $this->dir , 'config.toml' , fn( array $config ) => $config + [ 'extra' => true ] ) ;
        $this->assertSame([ 'app' => [ 'name' => 'system' ] , 'extra' => true ] , $config ) ;
    }

    /**
     * @throws TomlError
     */
    public function testInitConfigWithEmptyBasePathAndMissingFileReturnsEmptyArray()
    {
        $config = initConfig( file : 'oihana-definitely-missing-' . uniqid() . '.toml' ) ;
        $this->assertSame( [] , $config ) ;
    }
}
