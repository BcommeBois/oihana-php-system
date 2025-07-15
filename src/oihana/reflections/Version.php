<?php

namespace oihana\reflections;

use oihana\enums\Char;
use oihana\interfaces\Equatable;

/**
 * Class Version
 *
 * Represents a software version using four components: major, minor, build, and revision.
 * These components are internally encoded into a single 32-bit integer for compact storage and efficient comparison.
 *
 * ### Usage Example:
 * <code>
 * use oihana\reflections\Version ;
 *
 * $version1 = new Version( 2 , 1 , 1 , 110 ) ;
 * $version2 = new Version( 3 , 1 , 1 , 110 ) ;
 * $version3 = new Version( 1 , 2 , 3 , 4 ) ;
 *
 * $version1->major = 3 ;
 *
 * echo('version   : ' . $version1 ) ;
 * echo( 'major    : ' . $version1->major ) ;
 * echo( 'minor    : ' . $version1->minor ) ;
 * echo( 'build    : ' . $version1->build ) ;
 * echo( 'revision : ' . $version1->revision ) ;
 *
 * echo( 'version 1 : ' . $version1->valueOf() ) ;
 * echo( 'version 2 : ' . $version2->valueOf() ) ;
 * echo( 'version 3 : ' . $version3->valueOf() ) ;
 *
 * echo( "equals( 'toto' )    : " . ($version1->equals('toto')    ? 'true' : 'false' )) ;
 * echo( "equals( $version2 ) : " . ($version1->equals($version2) ? 'true' : 'false' )) ;
 * echo( "equals( $version3 ) : " . ($version1->equals($version3) ? 'true' : 'false' )) ;
 * </code>
 * ### Key Features:
 * - Supports dynamic property access (`$version->major`, `$version->minor`, etc.) through magic methods.
 * - Efficiently encodes and decodes version components using bitwise operations.
 * - Customizable string representation via the `separator` and `fields` properties.
 * - Implements equality checking through the `equals()` method.
 * - Provides a `fromString()` static method for instantiating versions from formatted strings.
 */
class Version implements Equatable
{
    /**
     * Creates a new Version instance.
     * @param int $major
     * @param int $minor
     * @param int $build
     * @param int $revision
     */
    public function __construct( int $major = 0, int $minor = 0, int $build = 0, int $revision = 0 )
    {
        $this->_value = ( $major << 28 ) | ( $minor << 24 ) | ( $build << 16 ) | $revision ;
    }

    /**
     * The fields limit.
     */
    public int $fields = 0 ;

    /**
     * The separator expression.
     */
    public string $separator = Char::DOT ;

    /**
     * We don't really need an equals method as we override the valueOf, we can do something as
     * <pre>
     * $v1 = new Version( 1,0,0,0 );
     * $v2 = new Version( 1,0,0,0 );
     * echo( json_encode( v1->equals( v2 ) ) ) ; //true
     * </pre>
     * A cast to Number/int force the valueOf, not ideal but sufficient, and the same for any other operators.
     * But as we keep Equatable for now, then we have no reason to not use it.
     */
    public function equals( mixed $value ) :bool
    {
        if( $value instanceof Version )
        {
            return $this->_value == $value->valueOf() ;
        }

        return false ;
    }

    /**
     * The build component value of this version.
     * @var int
     */
    public int $build
    {
        get => $this->RRR( ( $this->_value & 0x00FF0000 ) , 16 ) ;
        set( int $value )
        {
            $this->_value = ( $this->_value & 0xFF00FFFF ) | ( $value << 16 ) ;
        }
    }

    /**
     * The major component value of this version.
     */
    public int $major
    {
        get => $this->RRR( $this->_value , 28 ) ;
        set( int $value )
        {
            $this->_value = ( $this->_value & 0x0FFFFFFF ) | ( $value << 28 ) ;
        }
    }

    /**
     * The minor component value of this version.
     */
    public int $minor
    {
        get => $this->RRR( ( $this->_value & 0x0F000000 ) , 24 ) ;
        set( int $value )
        {
            $this->_value = ( $this->_value & 0xF0FFFFFF ) | ( $value << 24 ) ;
        }
    }

    /**
     * The revision component value of this version.
     */
    public int $revision
    {
        get => $this->_value & 0x0000FFFF ;
        set( int $value )
        {
            $this->_value = ( $this->_value & 0xFFFF0000 ) | $value;
        }
    }

    /**
     * Returns a version representation.
     * @param string $value
     * @param string $separator
     * @return string|null
     */
    public static function fromString( string $value , string $separator = Char::DOT ) :?string
    {
        if( $value == null || $value == Char::EMPTY )
        {
            return null ;
        }

        $v = new Version() ;

        if( strpos( $value , $separator ) > -1 )
        {
            $values = explode( $separator , $value ) ;
            $len    = count( $values ) ;

            if( $len > 0 )
            {
                $v->major = (int) $values[0] ;
            }

            if( $len > 1 )
            {
                $v->minor = (int) $values[1] ;
            }

            if( $len > 2 )
            {
                $v->build = (int) $values[2] ;
            }

            if( $len > 3 )
            {
                $v->revision = (int) $values[3] ;
            }
        }
        else
        {
            $vv = (int) $value ;
            if( $vv != 0 )
            {
                $v->major = $vv ;
            }
            else
            {
                $v = null ;
            }
        }

        return (string) $v ;
    }


    /**
     * Returns the string representation of the object.
     * @return string The string representation of the object.
     */
    public function __toString() :string
    {
        $data = [ $this->major , $this->minor , $this->build , $this->revision ] ;

        if( ( $this->fields > 0 ) && ( $this->fields < 5 ) )
        {
            $data = array_slice( $data , 0 , $this->fields ) ;
        }
        else
        {
            $l = count( $data ) ;
            for( $i = $l-1 ; $i>0 ; $i-- )
            {
                if( $data[$i] == 0 )
                {
                    array_pop( $data ) ;
                }
                else
                {
                    break;
                }
            }
        }

        return implode( $this->separator , $data ) ;
    }

    /**
     * Returns the primitive value of the object.
     * @return int The primitive value of the object.
     */
    public function valueOf():int
    {
        return $this->_value ;
    }

    /**
     * @var int
     */
    private int $_value ;

    /**
     * Emulates the >>> binary operator.
     */
    private function RRR( int $a , int $b ) :int
    {
        return (int) ( (float) $a / pow( 2 , $b ) );
    }
}
