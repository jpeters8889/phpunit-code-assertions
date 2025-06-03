<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures;

use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;

class MockAssertable implements Assertable
{
    public function assert(PendingFile $file): void
    {
        // TODO: Implement assert() method.
    }
}
