<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Helpers;

use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;

readonly class AssertablesToTestDto
{
    /**
     * @param class-string<Assertable> $assertable
     */
    public function __construct(
        public string $testName,
        public string $assertable,
        public string $method,
        public bool $negate = false,
        public array $args = [],
        public ?string $builderParam = null,
    ) {
        //
    }
}
