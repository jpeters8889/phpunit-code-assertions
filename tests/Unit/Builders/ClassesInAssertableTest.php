<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreInterfaces;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToImplement;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToUse;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Builders\Builder;
use Jpeters8889\PhpUnitCodeAssertions\Builders\ClassesInAssertable;
use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Contracts\MyInterface;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Helpers\AssertablesToTestDto;
use PHPUnit\Framework\Attributes\Test;

class ClassesInAssertableTest extends BuilderTestCase
{
    protected function makeBuilder(string $pathOrNamespace): Builder
    {
        return new ClassesInAssertable($pathOrNamespace);
    }

    #[Test]
    public function itDoesntLoadFilesToTestThatArentClasses(): void
    {
        $builder = $this->makeBuilder('tests/Fixtures');

        $invadedBuilder = invade($builder);

        $files = $invadedBuilder
            ->collectFilesToAssertAgainst()
            ->map(fn(PendingFile $file) => $file->fileName)
            ->toArray();

        $this->assertNotContains('functions.php', $files);
    }

    public static function getAssertablesToQueue(): Collection
    {
        return CodeAssertableTest::getAssertablesToQueue()->push(
            new AssertablesToTestDto(
                testName: 'are traits assertable',
                assertable: AreTraits::class,
                method: 'areTraits',
                builderParam: 'tests/Fixtures/Traits',
            ),
            new AssertablesToTestDto(
                testName: 'are not traits assertable',
                assertable: AreTraits::class,
                method: 'areNotTraits',
                negate: true,
                builderParam: 'tests/Helpers',
            ),
            new AssertablesToTestDto(
                testName: 'are classes assertable',
                assertable: AreClasses::class,
                method: 'areClasses',
                builderParam: 'tests/Helpers',
            ),
            new AssertablesToTestDto(
                testName: 'are not classes assertable',
                assertable: AreClasses::class,
                method: 'areNotClasses',
                negate: true,
                builderParam: 'tests/Fixtures/Traits',
            ),
            new AssertablesToTestDto(
                testName: 'are interfaces assertable',
                assertable: AreInterfaces::class,
                method: 'areInterfaces',
                builderParam: 'tests/Fixtures/Contracts',
            ),
            new AssertablesToTestDto(
                testName: 'are not interfaces assertable',
                assertable: AreInterfaces::class,
                method: 'areNotInterfaces',
                negate: true,
                builderParam: 'tests/Fixtures/Traits',
            ),
            new AssertablesToTestDto(
                testName: 'are contracts assertable',
                assertable: AreInterfaces::class,
                method: 'areContracts',
                builderParam: 'tests/Fixtures/Contracts',
            ),
            new AssertablesToTestDto(
                testName: 'are not contracts assertable',
                assertable: AreInterfaces::class,
                method: 'areNotContracts',
                negate: true,
                builderParam: 'tests/Fixtures/Traits',
            ),
            new AssertablesToTestDto(
                testName: 'to implement assertable',
                assertable: ToImplement::class,
                method: 'toImplement',
                args: [MyInterface::class],
                builderParam: 'tests/Fixtures/Feature',
            ),
            new AssertablesToTestDto(
                testName: 'to not implement assertable',
                assertable: ToImplement::class,
                method: 'toNotImplement',
                negate: true,
                args: [MyInterface::class],
                builderParam: 'tests/Fixtures/Traits',
            ),
            new AssertablesToTestDto(
                testName: 'to use assertable',
                assertable: ToUse::class,
                method: 'toUse',
                args: [MyTrait::class],
                builderParam: 'tests/Fixtures/Feature',
            ),
            new AssertablesToTestDto(
                testName: 'to not use assertable',
                assertable: ToUse::class,
                method: 'toNotUse',
                negate: true,
                args: [MyTrait::class],
                builderParam: 'tests/Fixtures/Traits',
            ),
        );
    }
}
