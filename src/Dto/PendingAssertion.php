<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Dto;

use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;

readonly class PendingAssertion
{
    /**
     * @param class-string<Assertable> $assertable
     * @param array<mixed> $args
     * @param array<string|class-string> $except
     */
    public function __construct(
        public string $assertable,
        public bool $negate = false,
        public array $args = [],
        public array $except = [],
    ) {
        //
    }
}
