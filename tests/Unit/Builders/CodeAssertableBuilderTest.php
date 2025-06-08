<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasStrictTypes;
use Jpeters8889\PhpUnitCodeAssertions\Builders\AssertableBuilder;
use Jpeters8889\PhpUnitCodeAssertions\Builders\CodeAssertableBuilder;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Helpers\AssertablesToTestDto;
use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;

class CodeAssertableBuilderTest extends AssertableBuilderTestCase
{
    protected function makeBuilder(string $pathOrNamespace): AssertableBuilder
    {
        return new CodeAssertableBuilder($pathOrNamespace);
    }

    public static function getAssertablesToQueue(): Collection
    {
        return collect([
            new AssertablesToTestDto(
                testName: 'uses functions assertable',
                assertable: UsesFunctions::class,
                method: 'usesFunctions',
                args: [['ucwords']],
            ),
            new AssertablesToTestDto(
                testName: 'does not use functions assertable',
                assertable: UsesFunctions::class,
                method: 'doesNotUseFunctions',
                negate: true,
                args: [['assert']],
            ),
            new AssertablesToTestDto(
                testName: 'uses strict types assertable',
                assertable: HasStrictTypes::class,
                method: 'toUseStrictTypes',
                builderParam: 'tests/Fixtures/StrictClass',
            ),
            new AssertablesToTestDto(
                testName: 'does not use strict types assertable',
                assertable: HasStrictTypes::class,
                method: 'toNotUseStrictTypes',
                negate: true,
                builderParam: 'tests/Fixtures/BaseClass',
            ),
        ]);
    }
}
