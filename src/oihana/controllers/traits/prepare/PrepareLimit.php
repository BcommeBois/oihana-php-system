<?php

namespace oihana\controllers\traits\prepare;

use oihana\controllers\traits\ApiTrait;
use oihana\controllers\traits\LimitTrait;
use oihana\enums\FilterOption;
use oihana\enums\Pagination;

use Psr\Http\Message\ServerRequestInterface as Request;

trait PrepareLimit
{
    use ApiTrait ,
        LimitTrait ;

    /**
     * Prepare and returns the 'limit' value.
     * @param Request|null $request
     * @param array $args
     * @param array|null $params
     * @param int $defaultValue
     * @param string $property
     * @return int
     */
    protected function prepareLimit
    (
        ?Request  $request ,
           array  $args         = [] ,
          ?array  &$params      = null ,
             int  $defaultValue = 0 ,
          string  $property     = Pagination::LIMIT
    ) :int
    {
        $value = $args[ $property ] ?? $this->{ $property } ?? null ;

        $flag = false ;
        if( isset( $request ) )
        {
            $param = $this->getQueryParam( $request , $property ); // query param only (not body).
            if( isset( $param ) )
            {
                $flag = true ;
                $value = filter_var
                (
                    $param ,
                    FILTER_VALIDATE_INT ,
                    [
                        FilterOption::OPTIONS =>
                        [
                            FilterOption::MIN_RANGE => intval( $this->minLimit ?? $this->api[ Pagination::MIN_LIMIT ] ?? 0   ) ,
                            FilterOption::MAX_RANGE => intval( $this->maxLimit ?? $this->api[ Pagination::MAX_LIMIT ] ?? 100 )
                        ]
                    ]
                );
            }
        }

        if( !is_int( $value ) )
        {
            $value = intval( $this->limit ?? $this->api[ $property ] ?? $defaultValue );
        }

        if( $flag )
        {
            $params[ $property ] = $value ;
        }

        return $value ;
    }

    /**
     * Prepare and returns the 'offset' value.
     * @param Request|null $request
     * @param array $args
     * @param array|null $params
     * @param int $defaultValue
     * @return int
     */
    protected function prepareOffset( ?Request $request , array $args = [] , ?array &$params = null , int $defaultValue = 0 ) :int
    {
        return $this->prepareLimit( $request , $args , $params , $defaultValue , Pagination::OFFSET ) ;
    }
}