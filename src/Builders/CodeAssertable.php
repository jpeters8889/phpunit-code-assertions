<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;

class CodeAssertable extends Builder
{
    public function usesFunctions(array $methods): self
    {
        return $this->addAssertion(UsesFunctions::class, args: [$methods]);
    }

    public function doesNotUseFunctions(array $methods): self
    {
        return $this->addAssertion(UsesFunctions::class, negate: true, args: [$methods]);
    }
}
