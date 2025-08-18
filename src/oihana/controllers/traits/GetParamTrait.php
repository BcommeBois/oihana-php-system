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

use org\schema\constants\Prop;

trait GetParamTrait
{
    /**
     * The internal strategy to get body or query parameters.
     * @var string
     * @see GetParamTrait
     * @see HttpParamStrategy
     */
    public string $paramsStrategy = HttpParamStrategy::BOTH ;

    /**
     * Initialize the params strategy : 'both' (default), 'body' (only), 'query' (only).
     * @param string|array|null $strategy
     * @return static
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
     * Get a parameter in the body of the current request.
     * @param ?Request $request
     * @param string $name
     * @return ?object
     */
    public function getBodyParam( ?Request $request , string $name ) : ?string
    {
        if ( $request )
        {
            $params = (array) $request->getParsedBody();
            if ( isset( $params[ $name ] ) )
            {
                return $params[ $name ] ;
            }
        }
        return null  ;
    }

    /**
     * Get the parameters in the body of the current request.
     * @param ?Request $request
     * @param array $names
     * @return ?array
     */
    public function getBodyParams( ?Request $request , array $names ) :?array
    {
        if( $request )
        {
            $variables = [] ;
            $params = (array) $request->getParsedBody();
            foreach( $names as $name )
            {
                if( isset( $params[$name] ) )
                {
                    $variables[$name] = $params[$name] ;
                }
            }
            return $variables ;
        }
        return null  ;
    }

    /**
     * Get the parameters in the query or body of the current request.
     * @param ?Request $request
     * @param string $name The name of the parameter to search in the current request
     * @param array $default The default array reference to fill the value if the parameter is not find.
     * @param bool $throwable Indicates if the method thrown an exception if the parameter not exist in the query or body of the request (default false).
     * @return mixed The parameter value of a default value or null.
     * @throws NotFoundException
     */
    public function getParam( ?Request $request , string $name , array $default = [] , bool $throwable = false ) :mixed
    {
        if( $request )
        {
            if( $this->paramsStrategy == HttpParamStrategy::BOTH || $this->paramsStrategy == HttpParamStrategy::QUERY )
            {
                $params = $request->getQueryParams();
                if( isset( $params[ $name ] ) )
                {
                    return $params[ $name ] ;
                }
            }

            if( $this->paramsStrategy == HttpParamStrategy::BOTH || $this->paramsStrategy == HttpParamStrategy::BODY )
            {
                $params = (array) $request->getParsedBody();
                if( isset( $params[ $name ] ) )
                {
                    return $params[ $name ] ;
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
     * Get the parameters in the query or body of the current request.
     * @param ?Request $request
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
     * Get the parameter value in the body of the current request.
     * @param ?Request $request
     * @param string $name
     * @return ?string
     */
    public function getQueryParam( ?Request $request , string $name ) :?string
    {
        if( $request )
        {
            $params = $request->getQueryParams() ;
            if( isset( $params[ $name ] ) )
            {
                return $params[ $name ] ;
            }
        }
        return null  ;
    }

    /**
     * @throws NotFoundException
     */
    protected function getParamBool( ?Request $request , string $name , array $args = [] , ?bool $defaultValue = null , bool $throwable = false ) :?int
    {
        $value = $this->getParam( $request , $name , $args , $throwable ) ;
        return $value == Boolean::TRUE || $value == Boolean::FALSE ? ( $value == Boolean::TRUE ) : $defaultValue ;
    }

    /**
     * @throws NotFoundException
     */
    protected function getParamFloat(?Request $request , string $name , array $args = [] , ?float $defaultValue = null , bool $throwable = false ) :?float
    {
        $value = $this->getParam( $request , $name , $args , $throwable ) ;
        return isset($value) ? (float) $value : $defaultValue ;
    }

    /**
     * @throws NotFoundException
     */
    protected function getParamFloatWithRange(?Request $request , string $name , float $min , float $max , mixed $defaultValue = null , array $args = [] , bool $throwable = false ) :?float
    {
        return $this->getParamNumberWithRange( $request , $name , FILTER_VALIDATE_FLOAT , $min , $max , $defaultValue , $args , $throwable ) ;
    }

    /**
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
     * @param ?Request $request
     * @param string $name The name of the parameter.
     * @param null|string|GetModel $model The identifier of the model.
     * @param string $key The key in the collection to target to find the default value.
     * @param string|null $value The value to search in the model to returns the good key.
     * @param string $fields
     * @param string $property The name of the property to extract in the model result.
     * @param bool $throwable
     * @return mixed
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function getParamDefaultValueInModel( ?Request $request , string $name , null|string|GetModel $model , string $key , ?string $value = null , string $fields = Prop::_KEY , string $property = Prop::_KEY , bool $throwable = false ) :mixed
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
                    $this->logger->error( __METHOD__ . ' failed, no document find in the model ' . $model . ' with the key:' . $key . ' and the value: ' . $value ) ;
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
     * @throws NotFoundException
     */
    protected function getParamNumberWithRange( ?Request $request , string $name , int $filter , int $min , int $max , null|int|float $defaultValue = null , array $args = [] , bool $throwable = false ) :?int
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