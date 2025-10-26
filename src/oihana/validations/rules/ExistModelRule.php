<?php

namespace oihana\validations\rules ;

use oihana\enums\Char;
use oihana\models\enums\ModelParam;
use oihana\models\interfaces\ExistModel;

use org\schema\constants\Schema;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use Somnambulist\Components\Validation\Exceptions\ParameterException;

/**
 * An abstract rule to defines rules with an internal DI container reference.
 */
class ExistModelRule extends ContainerRule
{
    /**
     * Creates a new ContainerRule instance.
     *
     * @param ContainerInterface $container The DI container reference.
     * @param array|string       $init      The options to passed-in the rule.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct
    (
        ContainerInterface $container   ,
        string|array       $init = []   ,
        ?string            $key  = null ,
    )
    {
        if( is_string( $init ) )
        {
            $init =
            [
                self::KEY   => $key ,
                self::MODEL => $init != Char::EMPTY ? $init : null
            ] ;
        }
        parent::__construct( $container , $init ) ;
        $this->key   ( $init[ self::KEY   ] ?? $key ?? self::DEFAULT_KEY )
             ->model ( $init[ self::MODEL ] ?? null                      ) ;
    }

    /**
     * The default 'key' value.
     */
    public const string DEFAULT_KEY = Schema::ID ;

    /**
     * The 'key' parameter key.
     */
    public const string KEY = 'key' ;

    /**
     * The 'model' parameter key.
     */
    public const string MODEL = 'model' ;

    /**
     * The internal list of fillable parameters.
     * @var array
     */
    protected array $fillableParams = [ self::MODEL , self::KEY ] ;

    /**
     * The internal message pattern.
     * @var string
     */
    protected string $message = ":attribute is not registered in the model: ':model' with the value: :value";

    /**
     * Checks whether the given value satisfies the condition.
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value satisfies the condition.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ParameterException
     */
    public function check( mixed $value ): bool
    {
        $this->assertHasRequiredParameters( $this->fillableParams );

        $model = $this->parameter(self::MODEL ) ;

        if( !is_string( $model ) || !$this->container->has( $model ) )
        {
            return false ;
        }

        $key   = $this->parameter(self::KEY ) ;
        $model = $this->container->get( $model ) ;

        if( $model instanceof ExistModel )
        {
            return $model->exist
            ([
                ModelParam::KEY   => $key ,
                ModelParam::VALUE => $value ,
            ]) ;
        }

        return false ;
    }

    /**
     * Defines the optional key to find the ressource in the model.
     *
     * @param ?string $value The key value.
     *
     * @return $this
     */
    public function key( ?string $value = null ) :static
    {
        $this->params[ self::KEY ] = $value ;
        return $this ;
    }

    /**
     * Defines the model identifier to find it in the DI container.
     *
     * @param ?string $value The identifier of the model definition in the DI container.
     *
     * @return $this
     */
    public function model( ?string $value = null ) :static
    {
        $this->params[ self::MODEL ] = $value ;
        return $this ;
    }
}