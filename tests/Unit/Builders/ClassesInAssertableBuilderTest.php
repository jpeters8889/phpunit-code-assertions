<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Builders;

use Hamcrest\Type\IsResource;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreInterfaces;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasMethods;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasSuffix;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\IsAbstract;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\IsFinal;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\IsReadOnly;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\OnlyHaveMethod;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToExtend;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToImplement;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToUse;
use Jpeters8889\PhpUnitCodeAssertions\Builders\AssertableBuilder;
use Jpeters8889\PhpUnitCodeAssertions\Builders\ClassesInAssertableBuilder;
use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\AbstractClass\AbstractClass;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Contracts\MyInterface;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Helpers\AssertablesToTestDto;
use PHPUnit\Framework\Attributes\Test;

class ClassesInAssertableBuilderTest extends AssertableBuilderTestCase
{
    protected function makeBuilder(string $pathOrNamespace): AssertableBuilder
    {
        return new ClassesInAssertableBuilder($pathOrNamespace);
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
        return CodeAssertableBuilderTest::getAssertablesToQueue()->push(
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
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'are classes assertable',
                assertable: AreClasses::class,
                method: 'areClasses',
                builderParam: 'tests/Fixtures/BaseClass',
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
                builderParam: 'tests/Fixtures/ClassUsesInterface',
            ),
            new AssertablesToTestDto(
                testName: 'to not implement assertable',
                assertable: ToImplement::class,
                method: 'toNotImplement',
                negate: true,
                args: [MyInterface::class],
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to use assertable',
                assertable: ToUse::class,
                method: 'toUse',
                args: [MyTrait::class],
                builderParam: 'tests/Fixtures/ClassUsesTrait',
            ),
            new AssertablesToTestDto(
                testName: 'to not use assertable',
                assertable: ToUse::class,
                method: 'toNotUse',
                negate: true,
                args: [MyTrait::class],
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to extend assertable',
                assertable: ToExtend::class,
                method: 'toExtend',
                args: [AbstractClass::class],
                builderParam: 'tests/Fixtures/ClassExtend',
            ),
            new AssertablesToTestDto(
                testName: 'to not extend assertable',
                assertable: ToExtend::class,
                method: 'toNotExtend',
                negate: true,
                args: [AbstractClass::class],
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to be abstract assertable',
                assertable: IsAbstract::class,
                method: 'toBeAbstract',
                builderParam: 'tests/Fixtures/AbstractClass',
            ),
            new AssertablesToTestDto(
                testName: 'to not be abstract assertable',
                assertable: IsAbstract::class,
                method: 'toNotBeAbstract',
                negate: true,
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to have methods assertable',
                assertable: HasMethods::class,
                method: 'toHaveMethods',
                args: [[['hello', 'bye']]],
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to not have methods assertable',
                assertable: HasMethods::class,
                method: 'toNotHaveMethods',
                negate: true,
                args: [[['foo', 'bar']]],
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to have method assertable',
                assertable: HasMethods::class,
                method: 'toHaveMethod',
                args: ['hello'],
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to not have method assertable',
                assertable: HasMethods::class,
                method: 'toNotHaveMethod',
                negate: true,
                args: ['foo'],
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to be invokable assertable',
                assertable: HasMethods::class,
                method: 'toBeInvokable',
                builderParam: 'tests/Fixtures/InvokableClass',
            ),
            new AssertablesToTestDto(
                testName: 'to not be invokable assertable',
                assertable: HasMethods::class,
                method: 'toBeNotInvokable',
                negate: true,
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to only have method assertable',
                assertable: OnlyHaveMethod::class,
                method: 'toOnlyHaveMethod',
                args: ['__invoke'],
                builderParam: 'tests/Fixtures/InvokableClass',
            ),
            new AssertablesToTestDto(
                testName: 'to not only have method assertable',
                assertable: OnlyHaveMethod::class,
                method: 'toNotOnlyHaveMethod',
                negate: true,
                args: ['hello'],
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to only be invokable assertable',
                assertable: OnlyHaveMethod::class,
                method: 'toOnlyBeInvokable',
                builderParam: 'tests/Fixtures/InvokableClass',
            ),
            new AssertablesToTestDto(
                testName: 'to not only be invokable assertable',
                assertable: OnlyHaveMethod::class,
                method: 'toNotOnlyBeInvokable',
                negate: true,
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to be final assertable',
                assertable: IsFinal::class,
                method: 'toBeFinal',
                builderParam: 'tests/Fixtures/FinalClass',
            ),
            new AssertablesToTestDto(
                testName: 'to be not final assertable',
                assertable: IsFinal::class,
                method: 'toBeNotFinal',
                negate: true,
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to be read only assertable',
                assertable: IsReadOnly::class,
                method: 'toBeReadOnly',
                builderParam: 'tests/Fixtures/ReadOnlyClass',
            ),
            new AssertablesToTestDto(
                testName: 'to not be read only assertable',
                assertable: IsReadOnly::class,
                method: 'toBeNotReadOnly',
                negate: true,
                builderParam: 'tests/Fixtures/BaseClass',
            ),
            new AssertablesToTestDto(
                testName: 'to have suffix assertable',
                assertable: HasSuffix::class,
                method: 'toHaveSuffix',
                args: ['Class'],
                builderParam: 'tests/Fixtures/FinalClass',
            ),
            new AssertablesToTestDto(
                testName: 'to not have suffix assertable',
                assertable: HasSuffix::class,
                method: 'toNotHaveSuffix',
                negate: true,
                args: ['Foo'],
                builderParam: 'tests/Fixtures/BaseClass',
            ),
        );
    }
}
