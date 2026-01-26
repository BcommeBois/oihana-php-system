<?php

namespace oihana\controllers\traits;

use Exception;
use Imagick;
use ImagickException;
use ImagickPixel;

use oihana\enums\http\HttpHeader;
use oihana\graphics\AspectRatio;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait ImageTrait
{
    use StatusTrait ;

    /**
     * The default image compression of the output image.
     */
    public int $compression = Imagick::COMPRESSION_JPEG ;

    public function getImagesRoot():string
    {
        return $this->config['images']['root'] ?? '' ; // FIXME use a property to initialize the default image root path.
    }

    /**
     * @throws ImagickException
     */
    public function getImageDimensions( Imagick|string $image ) :?array
    {
        if( is_string( $image ) )
        {
            $image = new Imagick( $image ) ;
        }
        return [ 'width' => $image->getImageWidth() , 'height' => $image->getImageHeight() ] ;
    }

    /**
     * @throws ImagickException
     */
    public function getImageHeight( Imagick|string $image ) :int
    {
        if( is_string( $image ) )
        {
            $image = new Imagick( $image ) ;
        }
        return $image->getImageHeight() ;
    }

    /**
     * @throws ImagickException
     */
    public function getImageWidth( Imagick|string $image ) :int
    {
        if( is_string( $image ) )
        {
            $image = new Imagick( $image ) ;
        }
        return $image->getImageWidth() ;
    }

    protected array $image_response_default_options =
    [
        'contentDisposition'    => null ,
        'format'                => 'jpg' ,
        'useContentDisposition' => false ,
        'useContentLength'      => true ,
        'useContentType'        => true
    ];

    /**
     * Returns an Image response
     * @param ?Request $request Optional PSR-7 Request object.
     * @param Response $response The PSR-7 Response object.
     * @param string $file
     * @param array $options
     * @return Response
     */
    public function imageResponse
    (
        ?Request $request ,
        Response $response ,
        string $file ,
        array $options = []
    )
    : Response
    {
        try
        {
            $contentDisposition    = $options[ 'contentDisposition'    ] ?? null  ;
            $format                = $options[ 'format'                ] ?? 'jpg' ;
            $useContentDisposition = $options[ 'useContentDisposition' ] ?? false ;
            $useContentLength      = $options[ 'useContentLength'      ] ?? true  ;
            $useContentType        = $options[ 'useContentType'        ] ?? true  ;

            if( $useContentType )
            {
                $format = !empty( $format ) ? 'image/' . $format : mime_content_type( $file ) ;
                $response = $response->withHeader( HttpHeader::CONTENT_TYPE  , $format ) ;
            }

            if( $useContentLength )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_LENGTH , filesize( $file ) ) ;
            }

            if( $useContentDisposition )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_DISPOSITION , $contentDisposition ) ;
            }

            $response->getBody()->write( file_get_contents( $file ) );

            return $response ;
        }
        catch( Exception $e )
        {
            return $this->fail( $request , $response , 500 ,  $e->getMessage() ) ;
        }
    }

    protected array $imagick_response_default_options =
    [
        'compression' => Imagick::COMPRESSION_JPEG ,
        'quality'     => 70    ,
        'gray'        => false ,
        'strip'       => false ,
    ];

    /**
     * Returns an Image response with options to transform the image before.
     * @param Response $response
     * @param mixed $image
     * @param array $options
     * @return Response
     */
    public function imagickResponse( Response $response , string|Imagick $image , array $options = [] ): Response
    {
        try
        {
            [
                'compression'           => $compression ,
                'contentDisposition'    => $contentDisposition ,
                'quality'               => $quality ,
                'gray'                  => $gray ,
                'format'                => $format ,
                'strip'                 => $strip ,
                'useContentDisposition' => $useContentDisposition ,
                'useContentLength'      => $useContentLength ,
                'useContentType'        => $useContentType
            ]
            = [
                ...$this->image_response_default_options ,
                ...$this->imagick_response_default_options ,
                ...$options
            ] ;

            if( is_string( $image ) )
            {
                $image = new Imagick( $image ) ;
            }

            if ( $gray )
            {
                $image->modulateImage(100, 0, 100);
            }

            if ( !empty( $format ) )
            {
                $image->setImageFormat( $format );
            }

            $image->setImageCompression( $compression );
            $image->setImageCompressionQuality( $quality );

            if ( $strip )
            {
                $image->stripImage();
            }

            if( $useContentType )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_TYPE , 'image/' . strtolower( $image->getImageFormat() ) ) ;
            }

            if( $useContentLength )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_LENGTH , strlen( $image->__toString() ) ) ;
            }

            if( $useContentDisposition )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_DISPOSITION , $contentDisposition ) ;
            }

            $response->getBody()->write( $image->getImageBlob() );

            $image->clear() ;

            return $response ;
        }
        catch( Exception $e )
        {
            return $this->fail( $response ,  500 , $e->getMessage() ) ;
        }
    }

    protected array $resize_options_default =
    [
        'maxHeight' => 1200 ,
        'maxWidth'  => 1920
    ];

    /**
     * Resize an image.
     * Ex: ../image?resize=true&w=50&h=50
     * @param Imagick|string|null $image The url of the image file or the Imagick object reference to transform.
     * @param ?int $w
     * @param ?int $h
     * @param array $options
     * @return string|Imagick|null
     * @throws ImagickException
     */
    public function resize( Imagick|string|null $image , ?int $w = null , ?int $h = null , array $options = [] ):string|Imagick|null
    {
        if( $image )
        {
            if( is_string( $image ) )
            {
                $image = new Imagick( $image ) ;
            }

            [
                'height'    => $height ,
                'maxHeight' => $maxHeight ,
                'maxWidth'  => $maxWidth ,
                'width'     => $width
            ]
            = [ ...$image->getImageGeometry() , ...$this->resize_options_default , ...$options ] ;

            if( ( $width > $maxWidth ) || ( $height > $maxHeight ) )
            {
                $image->resizeImage
                (
                    $maxWidth, $maxHeight, Imagick::FILTER_LANCZOS, 1.1, true
                ) ;
            }

            $hasW = is_int($w) && ( $w > 0 ) ;
            $hasH = is_int($h) && ( $h > 0 ) ;

            if( $hasW && $hasH )
            {
                $image->resizeImage( $w, $h, Imagick::FILTER_LANCZOS, 1.1, true );
            }
            elseif( $hasW || $hasH )
            {
                $ratio = new AspectRatio( $width , $height , true ) ;

                if( $hasW > 0 )
                {
                    $ratio->width = $w ;
                }
                elseif( $hasH > 0 )
                {
                    $ratio->height = $h ;
                }

                $image->resizeImage( $ratio->width , $ratio->height , Imagick::FILTER_LANCZOS, 1.1 , true ) ;
            }

        }

        return $image ;
    }

    /**
     * Apply a shadow over an image.
     * Ex: ../image?shadow=true
     * Ex: ../image?shadow=60,4,10,20
     * @param Imagick|string|null $image The url of the image file or the Imagick object reference to transform.
     * @param ?string $value
     * @return string|Imagick|null
     * @throws ImagickException
     */
    public function shadow( Imagick|string|null $image , null|string $value = null  ):string|Imagick|null
    {
        if( !empty( $value ) && $image !== null)
        {
            if( is_string( $image ) )
            {
                $image = new Imagick( $image ) ;
            }

            $shadow = explode( ',' , $value ) ;
            if( is_array( $shadow ) && ( count($shadow) == 4 ) )
            {
                [ $opacity, $sigma, $x, $y ] = $shadow ;
                $shadowImage = clone $image ;
                $shadowImage->setImageBackgroundColor( new ImagickPixel( 'black' ) ) ;
                $shadowImage->shadowImage( $opacity , $sigma , $x , $y ) ;
                $shadowImage->compositeImage( $image, Imagick::COMPOSITE_OVER, 0, 0 );
                return $shadowImage ;
            }
        }

        return $image ;
    }
}