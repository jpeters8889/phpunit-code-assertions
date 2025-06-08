<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Contracts;

use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;

interface Assertable
{
    /**
     * @param array<string|class-string> $except
     */
    public function assert(PendingFile $file, bool $negate = false, array $except = []): void;
}
