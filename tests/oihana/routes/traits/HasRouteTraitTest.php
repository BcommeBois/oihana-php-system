<?php

namespace tests\oihana\routes\traits;

use oihana\routes\enums\RouteFlag;
use oihana\routes\traits\HasRouteTrait;
use PHPUnit\Framework\TestCase;

class HasRouteTraitTest extends TestCase
{
    /**
     * Mock class using the trait.
     */
    protected object $mock;

    protected function setUp(): void
    {
        $this->mock = new class
        {
            use HasRouteTrait;

            public function callInitialize( array $init = [] ): void
            {
                $this->initializeFlags( $init ) ;
            }
        };
    }

    public function test_default_flags_are_true_by_default(): void
    {
        $this->mock->callInitialize([]);
        foreach ($this->getAllFlags() as $flag)
        {
            $this->assertTrue($this->mock->$flag, "Flag $flag should default to true");
        }
    }

    public function test_default_flag_can_disable_all(): void
    {
        $this->mock->callInitialize([RouteFlag::DEFAULT_FLAG => false]);

        foreach ($this->getAllFlags() as $flag)
        {
            $this->assertFalse($this->mock->$flag, "Flag $flag should be false when DEFAULT_FLAG=false");
        }
    }

    public function test_partial_initialization_overrides_some_flags(): void
    {
        $this->mock->callInitialize([
            RouteFlag::DEFAULT_FLAG => false,
            RouteFlag::HAS_GET      => true,
            RouteFlag::HAS_POST     => true,
        ]);

        $this->assertTrue($this->mock->hasGet);
        $this->assertTrue($this->mock->hasPost);

        // all others should remain false
        foreach ($this->getAllFlags(['hasGet', 'hasPost']) as $flag) {
            $this->assertFalse($this->mock->$flag, "Flag $flag should remain false");
        }
    }

    public function test_all_flags_can_be_individually_overridden(): void
    {
        $init =
        [
            RouteFlag::HAS_COUNT           => false,
            RouteFlag::HAS_DELETE          => true,
            RouteFlag::HAS_DELETE_MULTIPLE => false,
            RouteFlag::HAS_GET             => true,
            RouteFlag::HAS_LIST            => false,
            RouteFlag::HAS_PATCH           => true,
            RouteFlag::HAS_POST            => false,
            RouteFlag::HAS_PUT             => true,
        ];

        $this->mock->callInitialize($init);

        foreach ( $init as $const => $expected )
        {
            $this->assertSame( $expected , $this->mock->{ $const }, "Flag $const mismatch");
        }
    }

    protected function getAllFlags(array $exclude = []): array
    {
        $flags =
        [
            'hasCount', 'hasDelete', 'hasDeleteMultiple',
            'hasGet', 'hasList', 'hasPatch', 'hasPost', 'hasPut',
        ];
        return array_values(array_diff($flags, $exclude));
    }
}