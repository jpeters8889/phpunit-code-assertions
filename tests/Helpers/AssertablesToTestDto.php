<?php

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
        public array $args = [],
    )
    {
        //
    }
}
