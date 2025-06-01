<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\DoesNotUseFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Builders\CodeAssertable;
use Jpeters8889\PhpUnitCodeAssertions\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class CodeAssertableTest extends TestCase
{
    #[Test]
    #[DataProvider('assertablesToQueue')]
    public function itQueuesUpTheEachAssertable(string $method, array $args, string $assertableClass): void
    {
        $builder = new CodeAssertable('./../../Fixtures');

        $builder->$method(...$args);

        $assertions = $builder->getAssertionsToMake();

        $this->assertCount(1, $assertions);
        $this->assertInstanceOf($assertableClass, $assertions->first());
    }

    #[Test]
    #[DataProvider('assertablesToExecute')]
    public function itExecutesEachAssertionInTheShutdown(string $assertableClass): void
    {
        $mock = Mockery::spy($assertableClass);

        $builder = new CodeAssertable('./../../Fixtures');

        $builder->addAssertion($mock);

        unset($builder);

        $mock->shouldHaveReceived('assert')->once();

        $this->assertTrue(true);
    }

    #[Test]
    #[DataProvider('assertablesToExecute')]
    public function itCanExecuteEachAssertionByManuallyTriggering(string $assertableClass): void
    {
        $mock = Mockery::spy($assertableClass);

        $builder = new CodeAssertable('./../../Fixtures');

        $builder->addAssertion($mock)->executeAssertions();

        unset($builder);

        $mock->shouldHaveReceived('assert')->once();

        $this->assertTrue(true);
    }

    public static function assertablesToQueue(): array
    {
        return [
            'does not use functions assertable' => [
                'doesNotUseFunctions',
                [['assert']],
                DoesNotUseFunctions::class,
            ],
        ];
    }

    public static function assertablesToExecute(): array
    {
        return [
            'does not use functions assertable' => [
                DoesNotUseFunctions::class,
            ],
        ];
    }
}
