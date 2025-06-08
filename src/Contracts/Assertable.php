<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Contracts;

use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;

interface Assertable
{
    public function assert(PendingFile $file, bool $negate = false, array $except = []): void;
}
