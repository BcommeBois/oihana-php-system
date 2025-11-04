<?php

namespace tests\oihana\validations\rules\models;

use DI\Container;

use oihana\validations\rules\models\ExistModelRule;
use oihana\validations\rules\models\UniqueModelRule;
use PHPUnit\Framework\TestCase;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use tests\oihana\models\mocks\MockDocumentsModel;

use Somnambulist\Components\Validation\Exceptions\ParameterException;

final class UniqueModelRuleTest extends TestCase
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ParameterException
     */
    public function testUniqueModelRuleReturnsTrueForUniqueValue(): void
    {
        $model = new MockDocumentsModel();
        $model->addDocument(['id' => 1, 'email' => 'john@example.com']);

        $container = new Container();
        $container->set('user.model', $model);

        $rule = new UniqueModelRule
        (
            $container,
            [ ExistModelRule::MODEL => 'user.model', ExistModelRule::KEY => 'email' ]
        );

        // Valeur unique
        $this->assertTrue($rule->check('unique@example.com'));
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ParameterException
     */
    public function testUniqueModelRuleReturnsFalseForExistingValue(): void
    {
        $model = new MockDocumentsModel();
        $model->addDocument(['id' => 1, 'email' => 'john@example.com']);

        $container = new Container();
        $container->set('user.model', $model);

        $rule = new UniqueModelRule
        (
            $container,
            [ ExistModelRule::MODEL => 'user.model', ExistModelRule::KEY => 'email' ]
        );

        // Valeur déjà existante
        $this->assertFalse($rule->check('john@example.com'));
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ParameterException
     */
    public function testUniqueModelRuleWithCustomKey(): void
    {
        $model = new MockDocumentsModel();
        $model->addDocument(['id' => 1, 'username' => 'john']);

        $container = new Container();
        $container->set('user.model', $model);

        $rule = new UniqueModelRule
        (
            $container,
            [ ExistModelRule::MODEL => 'user.model', ExistModelRule::KEY => 'username' ]
        );

        $this->assertTrue($rule->check('uniqueUser'));
        $this->assertFalse($rule->check('john'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testUniqueModelRuleThrowsParameterExceptionIfModelMissing(): void
    {
        $container = new Container();

        $rule = new UniqueModelRule($container, []);
        $this->expectException(ParameterException::class);
        $rule->check('anything');
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ParameterException
     */
    public function testUniqueModelRuleEmptyValueIsTreatedAsUnique(): void
    {
        $model = new MockDocumentsModel();
        $model->addDocument(['id' => 1, 'email' => 'john@example.com']);

        $container = new Container();
        $container->set('user.model', $model);

        $rule = new UniqueModelRule
        (
            $container,
            [ ExistModelRule::MODEL => 'user.model', ExistModelRule::KEY => 'email' ]
        );

        // Valeur vide (null ou '')
        $this->assertTrue($rule->check(''));
        $this->assertTrue($rule->check(null));
    }

}