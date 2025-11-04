<?php

namespace tests\oihana\validations\rules\models;

use DI\Container;

use oihana\validations\rules\models\ExistModelRule;
use PHPUnit\Framework\TestCase;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use tests\oihana\models\mocks\MockDocumentsModel;

use Somnambulist\Components\Validation\Exceptions\ParameterException;

final class ExistModelRuleTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ParameterException
     */
    public function testBasicExistModelRule(): void
    {
        $model = new MockDocumentsModel() ;

        $model->addDocument( ['id' => 1 , 'name' => 'John' ] );

        // fill here the model

        $container = new Container() ;
        $container->set( 'model' , $model ) ;

        $rule = new ExistModelRule
        (
            $container ,
            [ ExistModelRule::MODEL => 'model' ]
        );

        $this->assertTrue( $rule->check( 1 ) ) ;
        $this->assertFalse( $rule->check( 'hello' ) ) ;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ParameterException
     */
    public function testExistModelRuleWithCustomKey(): void
    {
        $model = new MockDocumentsModel() ;

        $model->addDocument( ['id' => 1 , 'name' => 'John' ] );

        // fill here the model

        $container = new Container() ;
        $container->set( 'model' , $model ) ;

        $rule = new ExistModelRule
        (
            $container ,
            [
                ExistModelRule::MODEL => 'model' ,
                ExistModelRule::KEY   => 'name'  ,
            ]
        );

        $this->assertTrue( $rule->check( 'John' ) ) ;
        $this->assertFalse( $rule->check( 'hello' ) ) ;
    }

}