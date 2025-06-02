<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\DoesNotUseFunctions;

class CodeAssertable extends Builder
{
    public function doesNotUseFunctions(array $methods): self
    {
        return $this->addAssertion(DoesNotUseFunctions::class, [$methods]);
    }
}
