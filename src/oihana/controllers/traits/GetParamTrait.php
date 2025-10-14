<?php

namespace oihana\controllers\traits;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Http\Message\ServerRequestInterface as Request;

use oihana\controllers\enums\ControllerParam;
use oihana\enums\Boolean;
use oihana\enums\FilterOption;
use oihana\enums\http\HttpParamStrategy;
use oihana\models\interfaces\GetModel;
use oihana\traits\ContainerTrait;

use org\schema\constants\Prop;

use function oihana\core\accessors\getKeyValue;
use function oihana\core\accessors\hasKeyValue;
use function oihana\core\accessors\setKeyValue;

/**
 * Trait GetParamTrait
 *
 * Provides flexible access to request parameters from query strings and request bodies.
 * Supports dot notation for nested keys (e.g., 'geo.latitude', 'address.postalCode').
 *
 * It provides methods to:
 * - Get single parameters (`getParam`, `getBodyParam`, `getQueryParam`)
 * - Get multiple parameters at once (`getBodyParams`, `getParams`)
 * - Get typed parameters with optional range validation (`getParamInt`, `getParamFloat`, etc.)
 * - Fetch default values from a model if the parameter is not present (`getParamDefaultValueInModel`)
 *
 * Usage example:
 * ```php
 * $name = $this->getBodyParam($request, 'name');          // simple key
 * $lat  = $this->getBodyParam($request, 'geo.latitude'); // nested key
 * $data = $this->getBodyParams($request, ['name', 'geo.latitude']);
 * ```
 *
 * @package oihana\controllers\traits
 */
trait GetParamTrait
{
    use ContainerTrait ;

    /**
     * Strategy to fetch parameters: 'both' (default), 'body' only, or 'query' only.
     * @var string
     */
    public string $paramsStrategy = HttpParamStrategy::BOTH ;

    /**
     * Initialize the params strategy : 'both' (default), 'body' (only), 'query' (only).
     *
     * @param string|array|null $strategy $strategy Either a string strategy or an array with key ControllerParam::PARAMS_STRATEGY.
     *
     * @return static
     *
     * @see getParam The params strategy is used in the getParam() method.
     */
    public function initializeParamsStrategy( string|array|null $strategy = null ) :static
    {
        if( is_array( $strategy ) )
        {
            $strategy = $strategy[ ControllerParam::PARAMS_STRATEGY ] ?? $this->paramsStrategy ;
        }
        $this->paramsStrategy = HttpParamStrategy::includes( $strategy ) ? $strategy : $this->paramsStrategy ;
        return $this ;
    }

    /**
     * Get a single parameter from the request body.
     * Supports dot notation for nested values.
     *
     * @param Request|null $request
     * @param string       $name Parameter key, can be nested ('geo.latitude').
     *
     * @return mixed|null
     */
    public function getBodyParam( ?Request $request , string $name ) : ?string
    {
        if ( $request )
        {
            $params = (array) $request->getParsedBody();
            if ( hasKeyValue( $params , $name ) )
            {
                return getKeyValue( $params , $name ) ;
            }
        }
        return null  ;
    }

    /**
     * Get multiple parameters from the request body.
     * Only keys present in the body are returned, nested keys supported.
     *
     * @param Request|null $request
     * @param string[]     $names Array of keys to retrieve, can use dot notation.
     *
     * @return array|null Associative array of key => value.
     */
    public function getBodyParams( ?Request $request , array $names ) :?array
    {
        if( $request )
        {
            $variables = [] ;
            $params = (array) $request->getParsedBody();
            foreach( $names as $name )
            {
                if ( hasKeyValue( $params , $name ) )
                {
                    $variables = setKeyValue( $variables , $name , getKeyValue( $params , $name ) ) ;
                }
            }
            return $variables ;
        }
        return null  ;
    }

    /**
     * Get a single parameter from query or body according to the current strategy.
     * Supports dot notation for nested keys.
     *
     * @param Request|null $request
     * @param string       $name      Parameter key
     * @param array        $default   Default array to look up the value if not found
     * @param bool         $throwable If true, throws NotFoundException when parameter is not found
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function getParam
    (
        ?Request $request   ,
        string   $name      ,
        array    $default   = [] ,
        bool     $throwable = false
    )
    :mixed
    {
        if( $request )
        {
            if( $this->paramsStrategy == HttpParamStrategy::BOTH || $this->paramsStrategy == HttpParamStrategy::QUERY )
            {
                $params = $request->getQueryParams();
                if ( hasKeyValue( $params , $name ) )
                {
                    return getKeyValue( $params , $name ) ;
                }
            }

            if( $this->paramsStrategy == HttpParamStrategy::BOTH || $this->paramsStrategy == HttpParamStrategy::BODY )
            {
                $params = (array) $request->getParsedBody();
                if ( hasKeyValue( $params , $name ) )
                {
                    return getKeyValue($params, $name);
                }
            }
        }

        if( $throwable )
        {
            throw new NotFoundException( 'The parameter "' . $name . '" was not found.' ) ;
        }

        return $default[ $name ] ?? null  ;
    }

    /**
     * Get all query parameters or body parameters if query is empty.
     *
     * @param ?Request $request
     *
     * @return ?array
     */
    public function getParams( ?Request $request ) :?array
    {
        if( $request )
        {
            $params = $request->getQueryParams();
            if( is_countable($params) && count($params) > 0 )
            {
                return $params ;
            }
            return (array) $request->getParsedBody();
        }
        return null  ;
    }

    /**
     * Get a single parameter from query string.
     * Supports dot notation for nested keys.
     *
     * @param ?Request $request
     * @param string   $name
     *
     * @return ?string
     */
    public function getQueryParam( ?Request $request , string $name ) :?string
    {
        if( $request )
        {
            $params = $request->getQueryParams() ;
            if ( hasKeyValue( $params , $name ) )
            {
                return getKeyValue( $params , $name ) ;
            }
        }
        return null  ;
    }

    /**
     * Get a boolean parameter from request.
     * Returns null if not found and defaultValue is not set.
     *
     * @param  Request|null $request
     * @param  string       $name
     * @param  array        $args          Additional arguments (ignored)
     * @param  bool|null    $defaultValue
     * @param  bool         $throwable
     *
     * @return bool|null
     *
     * @throws NotFoundException
     */
    protected function getParamBool( ?Request $request , string $name , array $args = [] , ?bool $defaultValue = null , bool $throwable = false ) :?bool
    {
        $value = $this->getParam( $request , $name , $args , $throwable ) ;
        return $value == Boolean::TRUE || $value == Boolean::FALSE ? ( $value == Boolean::TRUE ) : $defaultValue ;
    }

    /**
     * Get a float parameter.
     *
     * @param Request|null $request
     * @param string $name
     * @param array $args
     * @param float|null $defaultValue
     * @param bool $throwable
     * @return float|null
     * @throws NotFoundException
     */
    protected function getParamFloat(?Request $request , string $name , array $args = [] , ?float $defaultValue = null , bool $throwable = false ) :?float
    {
        $value = $this->getParam( $request , $name , $args , $throwable ) ;
        return isset( $value ) ? (float) $value : $defaultValue ;
    }

    /**
     * @throws NotFoundException
     */
    protected function getParamFloatWithRange
    (
        ?Request $request ,
        string $name ,
        float $min ,
        float $max ,
        mixed $defaultValue = null ,
        array $args = [] ,
        bool $throwable = false
    )
    :?float
    {
        return $this->getParamNumberWithRange( $request , $name , FILTER_VALIDATE_FLOAT , $min , $max , $defaultValue , $args , $throwable ) ;
    }

    /**
     * Get an integer parameter.
     *
     * @param Request|null $request
     * @param string $name
     * @param array $args
     * @param int|null $defaultValue
     * @param bool $throwable
     * @return int|null
     * @throws NotFoundException
     */
    protected function getParamInt( ?Request $request , $name , array $args = [] , ?int $defaultValue = null , bool $throwable = false ) :?int
    {
        $value = $this->getParam( $request , $name , $args , $throwable ) ;
        return isset($value) ? (int) $value : $defaultValue ;
    }

    /**
     * @throws NotFoundException
     */
    protected function getParamIntWithRange(?Request $request , string $name , int $min , int $max , mixed $defaultValue = null , array $args = [] , bool $throwable = false ) :?int
    {
        return $this->getParamNumberWithRange( $request , $name , FILTER_VALIDATE_INT , $min , $max , $defaultValue , $args , $throwable) ;
    }

    /**
     * Generates the status property from the current Request or find it in the status model with the default label ('on' by default).
     *
     * @param ?Request             $request
     * @param string               $name      The name of the parameter.
     * @param null|string|GetModel $model     The identifier of the model.
     * @param string|null          $key       The key in the collection to target to find the default value.
     * @param string|null          $value     The value to search in the model to returns the good key.
     * @param string               $fields
     * @param string               $property  The name of the property to extract in the model result.
     * @param bool                 $throwable
     *
     * @return mixed
     *
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function getParamDefaultValueInModel
    (
        ?Request             $request   ,
        string               $name      ,
        null|string|GetModel $model     ,
        ?string              $key       = null ,
        ?string              $value     = null ,
        string               $fields    = Prop::_KEY ,
        string               $property  = Prop::_KEY ,
        bool                 $throwable = false
    )
    :mixed
    {
        $param = $this->getParam( $request , $name , [] , $throwable ) ;
        if( isset( $param ) )
        {
            return $param ;
        }
        elseif ( isset( $model ) )
        {
            if( is_string( $model ) )
            {
                if( $this->container->has( $model ) )
                {
                    $model = $this->container->get( $model ) ;
                }
                else
                {
                    $this->logger->error( __METHOD__ . ' failed, the model ' . $model . ' is not registered in the DI container.' ) ;
                }
            }

            if( $model instanceof GetModel )
            {
                $result = $model->get
                ([
                    ControllerParam::KEY    => $key    ,
                    ControllerParam::VALUE  => $value  ,
                    ControllerParam::FIELDS => $fields ,
                ]) ;

                if( $result )
                {
                    return $result->{ $property } ;
                }
                else
                {
                    $this->logger->error( __METHOD__ . ' failed, no document find in the model with the key:' . $key . ' and the value: ' . $value ) ;
                }

            }
            else
            {
                $this->logger->error( __METHOD__ . ' failed, the model ' . json_encode( $model ) . ' is not a valid GetModel instance.' ) ;
            }
        }
        return null ;
    }

    /**
     * Get a numeric parameter with a min/max range validation.
     *
     * @param Request|null $request
     * @param string $name
     * @param int $filter FILTER_VALIDATE_INT or FILTER_VALIDATE_FLOAT
     * @param int|float $min
     * @param int|float $max
     * @param int|float|null $defaultValue
     * @param array $args
     * @param bool $throwable
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    protected function getParamNumberWithRange
    (
        ?Request       $request ,
        string         $name ,
        int            $filter ,
        int            $min ,
        int            $max ,
        null|int|float $defaultValue = null ,
        array          $args         = [] ,
        bool           $throwable    = false
    )
    :mixed
    {
        $value = $this->getParam( $request , $name , $args , $throwable ) ;
        return filter_var( $value , $filter,
        [
            FilterOption::OPTIONS =>
            [
                FilterOption::DEFAULT   => $defaultValue ,
                FilterOption::MAX_RANGE => $max ,
                FilterOption::MIN_RANGE => $min ,
            ]
        ]) ;
    }

    /**
     * @throws NotFoundException
     */
    protected function getParamString
    (
        ?Request $request ,
        string   $name ,
        array    $args = [] ,
        ?string  $defaultValue = null ,
        bool     $throwable = false
    ) :?string
    {
        return $this->getParam( $request , $name , $args , $throwable ) ?? $defaultValue ;
    }
}