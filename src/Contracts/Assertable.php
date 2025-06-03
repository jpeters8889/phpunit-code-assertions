<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Contracts;

use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;

interface Assertable
{
    public function assert(PendingFile $file): void;
}
