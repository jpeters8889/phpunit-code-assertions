<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasStrictTypes;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;

/**
 * @method self hasStrictTypes()
 * @method self usesStrictTypes()
 */
class CodeAssertableBuilder extends AssertableBuilder
{
    /** @param string[] $functions */
    public function usesFunctions(array $functions): static
    {
        return $this->addAssertion(UsesFunctions::class, args: [$functions]);
    }

    /** @param string[] $functions */
    public function doesNotUseFunctions(array $functions): static
    {
        return $this->addAssertion(UsesFunctions::class, negate: true, args: [$functions]);
    }

    public function toUseStrictTypes(): static
    {
        return $this->addAssertion(HasStrictTypes::class);
    }

    public function toNotUseStrictTypes(): static
    {
        return $this->addAssertion(HasStrictTypes::class, negate: true);
    }

    public function methodAliases(): array
    {
        return [
            'hasStrictTypes' => 'toUseStrictTypes',
            'usesStrictTypes' => 'toUseStrictTypes',
        ];
    }
}
