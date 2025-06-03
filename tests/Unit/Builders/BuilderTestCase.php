<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MockAssertable;
use Jpeters8889\PhpUnitCodeAssertions\Builders\Builder;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Helpers\AssertablesToTestDto;
use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingAssertion;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\AssertableFactory;
use Jpeters8889\PhpUnitCodeAssertions\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class BuilderTestCase extends TestCase
{
    use GetsAbsolutePath;

    abstract protected function makeBuilder(string $pathOrNamespace): Builder;

    #[Test]
    public function itWillComputeTheAbsoluteAndLocalPathsFromANamespace(): void
    {
        $builder = $this->makeBuilder('Jpeters8889\PhpUnitCodeAssertions\Builders');

        $invadedBuilder = invade($builder);

        $this->assertEquals('src/Builders', $invadedBuilder->localPath);
        $this->assertEquals($this->getAbsolutePath('src/Builders'), $invadedBuilder->absolutePath);
    }

    #[Test]
    public function itSetsTheLocalPathAndCalculatesTheAbsolutePathIfGivenAPath(): void
    {
        $builder = $this->makeBuilder('tests/Fixtures');

        $invadedBuilder = invade($builder);

        $this->assertEquals('tests/Fixtures', $invadedBuilder->localPath);
        $this->assertEquals($this->getAbsolutePath('tests/Fixtures'), $invadedBuilder->absolutePath);
    }

    #[Test]
    public function itCanGetACollectionOfPendingFilesToAssertAgainst(): void
    {

    }

    #[Test]
    public function itCanAddAnAssertion(): void
    {
        $builder = $this->makeBuilder('tests/Fixtures');

        $this->assertEmpty($builder->getAssertionsToMake());

        $builder->addAssertion(MockAssertable::class);

        $this->assertNotEmpty($builder->getAssertionsToMake());
        $this->assertCount(1, $builder->getAssertionsToMake());
        $this->assertInstanceOf(PendingAssertion::class, $builder->getAssertionsToMake()->first());
    }

    /** @return Collection<int, AssertablesToTestDto> */
    abstract public static function getAssertablesToQueue(): Collection;

    #[Test]
    #[DataProvider('assertablesToQueue')]
    public function itQueuesUpTheEachAssertable(string $method, array $args, string $assertableClass): void
    {
        $builder = $this->makeBuilder('tests/Fixtures');

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
            ->withArgs(function($file) {
                $this->assertInstanceOf(PendingFile::class, $file);

                return true;
            })
            ->once();

        AssertableFactory::register($assertableClass, $mock->getMock());

        $builder = $this->makeBuilder('tests/Fixtures');

        $builder->addAssertion($assertableClass, [['foo']]);

        unset($builder);
    }

    #[Test]
    #[DataProvider('assertablesToExecute')]
    public function itCanExecuteEachAssertionByManuallyTriggering(string $assertableClass): void
    {
        $mock = Mockery::mock($assertableClass)
            ->shouldReceive('assert')
            ->withArgs(function($file) {
                $this->assertInstanceOf(PendingFile::class, $file);

                return true;
            })
            ->once();

        AssertableFactory::register($assertableClass, $mock->getMock());

        $builder = $this->makeBuilder('tests/Fixtures');

        $builder->addAssertion($assertableClass, [['foo']])->executeAssertions();
    }

    public static function assertablesToQueue(): array
    {
        return static::getAssertablesToQueue()
            ->mapWithKeys(fn(AssertablesToTestDto $assertableToTestDto) => [$assertableToTestDto->testName => [
                $assertableToTestDto->method,
                $assertableToTestDto->args,
                $assertableToTestDto->assertable
            ]])->toArray();
    }

    public static function assertablesToExecute(): array
    {
        return static::getAssertablesToQueue()
            ->mapWithKeys(fn(AssertablesToTestDto $assertableToTestDto) => [$assertableToTestDto->testName => [$assertableToTestDto->assertable]])
            ->toArray();
    }
}
