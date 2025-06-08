<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasStrictTypes;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;

class CodeAssertableBuilder extends AssertableBuilder
{
    public function usesFunctions(array $methods): self
    {
        return $this->addAssertion(UsesFunctions::class, args: [$methods]);
    }

    public function doesNotUseFunctions(array $methods): self
    {
        return $this->addAssertion(UsesFunctions::class, negate: true, args: [$methods]);
    }

    public function toUseStrictTypes(): self
    {
        return $this->addAssertion(HasStrictTypes::class);
    }

    public function toNotUseStrictTypes(): self
    {
        return $this->addAssertion(HasStrictTypes::class, negate: true);
    }
}
