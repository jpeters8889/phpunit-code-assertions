<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Builders;

use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\DoesNotUseFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Builders\CodeAssertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingAssertion;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\AssertableFactory;
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
        $builder = new CodeAssertable('tests/Fixtures');

        $builder->$method(...$args);

        $assertions = $builder->getAssertionsToMake();

        $this->assertCount(1, $assertions);
        $this->assertInstanceOf(PendingAssertion::class, $assertions->first());
        $this->assertEquals($assertableClass, $assertions->first()->assertable);
    }

    #[Test]
    #[DataProvider('assertablesToExecute')]
    public function itExecutesEachAssertionInTheShutdown(string $assertableClass): void
    {
        $mock = Mockery::mock($assertableClass)
            ->shouldReceive('assert')
            ->withArgs(function($files) {
                $this->assertInstanceOf(Collection::class, $files);
                $this->assertInstanceOf(PendingFile::class, $files->first());

                return true;
            })
            ->once();

        AssertableFactory::register($assertableClass, $mock->getMock());

        $builder = new CodeAssertable('tests/Fixtures');

        $builder->addAssertion($assertableClass, [['foo']]);

        unset($builder);
    }

    #[Test]
    #[DataProvider('assertablesToExecute')]
    public function itCanExecuteEachAssertionByManuallyTriggering(string $assertableClass): void
    {
        $mock = Mockery::mock($assertableClass)
            ->shouldReceive('assert')
            ->withArgs(function($files) {
                $this->assertInstanceOf(Collection::class, $files);
                $this->assertInstanceOf(PendingFile::class, $files->first());

                return true;
            })
            ->once();

        AssertableFactory::register($assertableClass, $mock->getMock());

        $builder = new CodeAssertable('tests/Fixtures');

        $builder->addAssertion($assertableClass, [['foo']])->executeAssertions();
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
