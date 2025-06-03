<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Builders\Builder;
use Jpeters8889\PhpUnitCodeAssertions\Builders\CodeAssertable;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Helpers\AssertablesToTestDto;
use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\DoesNotUseFunctions;

class CodeAssertableTest extends BuilderTestCase
{
    protected function makeBuilder(string $pathOrNamespace): Builder
    {
        return new CodeAssertable($pathOrNamespace);
    }

    public static function getAssertablesToQueue(): Collection
    {
        return collect([
            new AssertablesToTestDto(
                testName: 'does not use functions assertable',
                assertable: DoesNotUseFunctions::class,
                method: 'doesNotUseFunctions',
                args: [['assert']],
            )
        ]);
    }
}
