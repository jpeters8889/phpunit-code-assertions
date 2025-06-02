<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Contracts;

use Illuminate\Support\Collection;

interface Assertable
{
    public function assert(Collection $files): void;
}
