<?php

namespace tests\oihana\init;

use Exception;

use PHPUnit\Framework\TestCase;

use function oihana\init\initDefinitions;

class InitDefinitionsTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/oihana-init-definitions-' . uniqid() ;
        mkdir( $this->dir . '/sub' , 0o775 , true ) ;
        file_put_contents( $this->dir . '/a.php'     , "<?php return [ 'a' => 1 , 'shared' => 'a' ] ;" ) ;
        file_put_contents( $this->dir . '/sub/b.php' , "<?php return [ 'b' => 2 , 'shared' => 'b' ] ;" ) ;
    }

    protected function tearDown(): void
    {
        @unlink( $this->dir . '/a.php' ) ;
        @unlink( $this->dir . '/sub/b.php' ) ;
        @rmdir( $this->dir . '/sub' ) ;
        @rmdir( $this->dir ) ;
    }

    /**
     * @throws Exception
     */
    public function testInitDefinitionsMergesAllPhpFilesRecursively()
    {
        $definitions = initDefinitions( $this->dir ) ;

        $this->assertSame( 1 , $definitions['a'] ) ;
        $this->assertSame( 2 , $definitions['b'] ) ;
        $this->assertArrayHasKey( 'shared' , $definitions ) ;
    }
}
