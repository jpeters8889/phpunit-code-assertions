<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MockAssertable;
use Jpeters8889\PhpUnitCodeAssertions\Builders\AssertableBuilder;
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
use ReflectionClass;
use ReflectionFunction;
use ReflectionParameter;

abstract class AssertableBuilderTestCase extends TestCase
{
    use GetsAbsolutePath;

    abstract protected function makeBuilder(string $pathOrNamespace): AssertableBuilder;

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
        $builder = $this->makeBuilder('tests/Fixtures');

        $invadedBuilder = invade($builder);

        $this->assertInstanceOf(Collection::class, $invadedBuilder->collectFilesToAssertAgainst());
        $this->assertInstanceOf(PendingFile::class, $invadedBuilder->collectFilesToAssertAgainst()->first());
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
    public function itQueuesUpTheEachAssertable(AssertablesToTestDto $dto): void
    {
        $builder = $this->makeBuilder($dto->builderParam ?: 'tests/Fixtures');

        $builder->{$dto->method}(...$dto->args);

        $assertions = $builder->getAssertionsToMake();

        $this->assertCount(1, $assertions);
        $this->assertInstanceOf(PendingAssertion::class, $assertions->first());
        $this->assertEquals($dto->assertable, $assertions->first()->assertable);

        $invadedBuilder = invade($builder);
        $invadedBuilder->assertionsToMake = collect();
    }

    #[Test]
    #[DataProvider('assertablesToQueue')]
    public function itExecutesEachAssertionInTheShutdown(AssertablesToTestDto $dto): void
    {
        $mock = Mockery::mock($dto->assertable)
            ->shouldReceive('assert')
            ->withArgs(function ($file, $negate = false) use ($dto) {
                $this->assertInstanceOf(PendingFile::class, $file);
                $this->assertEquals($dto->negate, $negate);

                return true;
            })
            ->once();

        AssertableFactory::register($dto->assertable, $mock->getMock());

        $builder = $this->makeBuilder($dto->builderParam ?: 'tests/Fixtures');

        $builder->addAssertion($dto->assertable, $dto->negate, [['foo']]);

        unset($builder);
    }

    #[Test]
    #[DataProvider('assertablesToQueue')]
    public function itCanExecuteEachAssertionByManuallyTriggering(AssertablesToTestDto $dto): void
    {
        $mock = Mockery::mock($dto->assertable)
            ->shouldReceive('assert')
            ->withArgs(function ($file, $negate) use ($dto) {
                $this->assertInstanceOf(PendingFile::class, $file);
                $this->assertEquals($dto->negate, $negate);

                return true;
            })
            ->once();

        AssertableFactory::register($dto->assertable, $mock->getMock());

        $builder = $this->makeBuilder($dto->builderParam ?: 'tests/Fixtures');

        $builder->addAssertion($dto->assertable, $dto->negate, [['foo']])->executeAssertions();
    }

    #[Test]
    #[DataProvider('assertablesToQueue')]
    public function itCanChainOnAnExceptMethodToEachAssertable(AssertablesToTestDto $dto): void
    {
        $builder = $this->makeBuilder($dto->builderParam ?: 'tests/Fixtures');

        $builder->addAssertion($dto->assertable, $dto->negate, [['foo']]);

        $this->assertCount(1, $builder->getAssertionsToMake());

        /** @var PendingAssertion $pendingAssertion */
        $pendingAssertion = $builder->getAssertionsToMake()->first();

        $this->assertEquals([], $pendingAssertion->except);

        $builder->except(['foo']);

        $this->assertCount(1, $builder->getAssertionsToMake());

        /** @var PendingAssertion $pendingAssertion */
        $pendingAssertion = $builder->getAssertionsToMake()->first();

        $this->assertEquals(['foo'], $pendingAssertion->except);

        $invadedBuilder = invade($builder);
        $invadedBuilder->assertionsToMake = collect();
    }

    #[Test]
    public function itCanCallAssertablesViaAMethodAliasCall(): void
    {
        $builder = $this->makeBuilder('tests/Fixtures');
        $invadedBuilder = invade($builder);

        $mockedBuilder = Mockery::mock($builder::class);

        $classAliases = $invadedBuilder->methodAliases();

        if (empty($classAliases)) {
            $this->markTestSkipped($builder::class . ' has no aliased methods');
        }

        foreach ($classAliases as $alias => $method) {
            $callable = null;
            $args = null;

            if (is_array($method)) {
                [$method, $callable] = $method;

                // get the result of the arg transformer
                $argsTransformer = new ReflectionFunction($callable);
                $params = $argsTransformer->getParameters();

                $args = array_map(fn (ReflectionParameter $param) => match($param->getType()->getName()) {
                    'array' => ['foo'],
                    default => 'foo',
                }, $params);
            }

            // If we dont have any args, then figure out what the method wants
            if ( ! $args) {
                $reflectedBuilder = new ReflectionClass($builder);
                $parentMethod = $reflectedBuilder->getMethod($method);
                $params = $parentMethod->getParameters();

                $args = array_map(fn (ReflectionParameter $param) => match($param->getType()->getName()) {
                    'array' => ['foo'],
                    default => 'foo',
                }, $params);
            }

            $expectedArgs = $callable ? $callable(...$args) : $args;

            $mockedBuilder
                ->shouldReceive($method)
                ->once()
                ->withArgs(function (...$params) use ($expectedArgs) {
                    $this->assertCount(count($params), $expectedArgs);

                    foreach ($params as $index => $param) {
                        $this->assertEquals($expectedArgs[$index], $param);
                    }

                    return true;
                })
                ->getMock()
                ->shouldReceive($alias)
                ->withArgs(function (...$params) use ($args) {
                    foreach ($params as $index => $param) {
                        $this->assertEquals($args[$index], $param);
                    }

                    return true;
                })
                ->once()
                ->andReturn($mockedBuilder->$method(...$expectedArgs));

            $mockedBuilder->{$alias}(...$args);
        }
    }

    public static function assertablesToQueue(): array
    {
        return static::getAssertablesToQueue()
            ->mapWithKeys(fn (AssertablesToTestDto $dto) => [$dto->testName => [$dto]])
            ->toArray();
    }

    protected function tearDown(): void
    {
        AssertableFactory::clear();

        parent::tearDown();
    }
}
