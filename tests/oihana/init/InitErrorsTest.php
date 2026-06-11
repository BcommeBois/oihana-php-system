<?php

namespace tests\oihana\init;

use oihana\enums\IniOptions;
use oihana\reflect\exceptions\ConstantException;

use PHPUnit\Framework\TestCase;

use function oihana\init\initErrors;

class InitErrorsTest extends TestCase
{
    private int $originalLevel;

    private array $originalIni = [];

    protected function setUp(): void
    {
        $this->originalLevel = error_reporting() ;
        foreach ( [ IniOptions::DISPLAY_ERRORS , IniOptions::DISPLAY_STARTUP_ERRORS , IniOptions::ERROR_LOG ] as $key )
        {
            $this->originalIni[ $key ] = (string) ini_get( $key ) ;
        }
    }

    protected function tearDown(): void
    {
        error_reporting( $this->originalLevel ) ;
        foreach ( $this->originalIni as $key => $value )
        {
            ini_set( $key , $value ) ;
        }
    }

    /**
     * @throws ConstantException
     */
    public function testInitErrorsWithNullUsesDefaultErrorLevel()
    {
        initErrors( null , null , E_WARNING ) ;
        $this->assertSame( E_WARNING , error_reporting() ) ;
    }

    /**
     * @throws ConstantException
     */
    public function testInitErrorsAppliesIniSettings()
    {
        initErrors(
        [
            IniOptions::ERROR_REPORTING        => E_ERROR ,
            IniOptions::DISPLAY_ERRORS         => '1' ,
            IniOptions::DISPLAY_STARTUP_ERRORS => '1' ,
        ]) ;

        $this->assertSame( E_ERROR , error_reporting() ) ;
        $this->assertSame( '1' , ini_get( IniOptions::DISPLAY_ERRORS ) ) ;
        $this->assertSame( '1' , ini_get( IniOptions::DISPLAY_STARTUP_ERRORS ) ) ;
    }

    /**
     * @throws ConstantException
     */
    public function testInitErrorsPrependsLogRootPathToRelativeErrorLog()
    {
        initErrors([ IniOptions::ERROR_LOG => 'logs/php.log' ] , '/tmp/oihana-root/' ) ;
        $this->assertSame( '/tmp/oihana-root/logs/php.log' , ini_get( IniOptions::ERROR_LOG ) ) ;
    }

    /**
     * @throws ConstantException
     */
    public function testInitErrorsUsesErrorLogAsIsWithoutLogRootPath()
    {
        initErrors([ IniOptions::ERROR_LOG => '/tmp/oihana-absolute.log' ]) ;
        $this->assertSame( '/tmp/oihana-absolute.log' , ini_get( IniOptions::ERROR_LOG ) ) ;
    }
}
