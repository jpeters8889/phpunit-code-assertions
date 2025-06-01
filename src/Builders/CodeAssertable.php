<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\DoesNotUseFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;

class CodeAssertable
{
    /** @var Collection<int, Assertable> */
    protected Collection $assertionsToMake;

    protected bool $hasExecutedAssertions = false;

    public function __construct(protected string $path)
    {
        $this->assertionsToMake = collect();
    }

    public function doesNotUseFunctions(array $methods): self
    {
        $this->addAssertion(new DoesNotUseFunctions($this->path, $methods));

        return $this;
    }

    public function addAssertion(Assertable $assertion): self
    {
        $this->assertionsToMake->push($assertion);

        return $this;
    }

    public function getAssertionsToMake(): Collection
    {
        return $this->assertionsToMake;
    }

    public function executeAssertions(): void
    {
        $this->assertionsToMake->each(function ($assertion) {
            return $assertion->assert();
        });

        $this->hasExecutedAssertions = true;
    }

    public function __destruct()
    {
        if ($this->hasExecutedAssertions) {
            return;
        }

        $this->executeAssertions();
    }
}
