<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Dto;

use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;

readonly class PendingAssertion
{
    /**
     * @param class-string<Assertable> $assertable
     */
    public function __construct(
        public string $assertable,
        public array $args = [],
    )
    {
        //
    }
}
