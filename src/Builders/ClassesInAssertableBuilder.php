<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreInterfaces;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasMethods;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasSuffix;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\IsAbstract;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\IsFinal;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\IsReadOnly;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\OnlyHaveMethod;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToExtend;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToImplement;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToUse;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\Expr\Error;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class ClassesInAssertableBuilder extends CodeAssertableBuilder
{
    public function areTraits(): static
    {
        return $this->addAssertion(AreTraits::class);
    }

    public function areNotTraits(): static
    {
        return $this->addAssertion(AreTraits::class, negate: true);
    }

    public function areClasses(): static
    {
        return $this->addAssertion(AreClasses::class);
    }

    public function areNotClasses(): static
    {
        return $this->addAssertion(AreClasses::class, negate: true);
    }

    public function areInterfaces(): static
    {
        return $this->addAssertion(AreInterfaces::class);
    }

    public function areNotInterfaces(): static
    {
        return $this->addAssertion(AreInterfaces::class, negate: true);
    }

    public function areContracts(): static
    {
        return $this->areInterfaces();
    }

    public function areNotContracts(): static
    {
        return $this->areNotInterfaces();
    }

    public function toImplement(string $interface): static
    {
        return $this->addAssertion(ToImplement::class, args: [$interface]);
    }

    public function toNotImplement(string $interface): static
    {
        return $this->addAssertion(ToImplement::class, negate: true, args: [$interface]);
    }

    public function toUse(string $trait): static
    {
        return $this->addAssertion(ToUse::class, args: [$trait]);
    }

    public function toNotUse(string $trait): static
    {
        return $this->addAssertion(ToUse::class, negate: true, args: [$trait]);
    }

    public function toExtend(string $class): static
    {
        return $this->addAssertion(ToExtend::class, args: [$class]);
    }

    public function toNotExtend(string $class): static
    {
        return $this->addAssertion(ToExtend::class, negate: true, args: [$class]);
    }

    public function toBeAbstract(): static
    {
        return $this->addAssertion(IsAbstract::class);
    }

    public function toNotBeAbstract(): static
    {
        return $this->addAssertion(IsAbstract::class, negate: true);
    }

    /** @param string[] $methods */
    public function toHaveMethods(array $methods): static
    {
        return $this->addAssertion(HasMethods::class, args: $methods);
    }
    /** @param string[] $methods */
    public function toNotHaveMethods(array $methods): static
    {
        return $this->addAssertion(HasMethods::class, negate: true, args: $methods);
    }

    public function toHaveMethod(string $method): static
    {
        return $this->toHaveMethods([$method]);
    }

    public function toNotHaveMethod(string $method): static
    {
        return $this->toNotHaveMethods([$method]);
    }

    public function toBeInvokable(): static
    {
        return $this->toHaveMethod('__invoke');
    }

    public function toBeNotInvokable(): static
    {
        return $this->toNotHaveMethod('__invoke');
    }

    public function toOnlyHaveMethod(string $method): static
    {
        return $this->addAssertion(OnlyHaveMethod::class, args: [$method]);
    }

    public function toNotOnlyHaveMethod(string $method): static
    {
        return $this->addAssertion(OnlyHaveMethod::class, negate: true, args: [$method]);
    }

    public function toOnlyBeInvokable(): static
    {
        return $this->toOnlyHaveMethod('__invoke');
    }

    public function toNotOnlyBeInvokable(): static
    {
        return $this->toNotOnlyHaveMethod('__invoke');
    }

    public function toBeFinal(): static
    {
        return $this->addAssertion(IsFinal::class);
    }

    public function toBeNotFinal(): static
    {
        return $this->addAssertion(IsFinal::class, negate: true);
    }

    public function toBeReadOnly(): static
    {
        return $this->addAssertion(IsReadOnly::class);
    }

    public function toBeNotReadOnly(): static
    {
        return $this->addAssertion(IsReadOnly::class, negate: true);
    }

    public function toHaveSuffix(string $suffix): static
    {
        return $this->addAssertion(HasSuffix::class, args: [$suffix]);
    }

    public function toNotHaveSuffix(string $suffix): static
    {
        return $this->addAssertion(HasSuffix::class, negate: true, args: [$suffix]);
    }

    protected function isFileTestable(SplFileInfo $file): bool
    {
        try {
            $ast = PhpFileParser::parse($file->getContents());

            $classes = (new NodeFinder())->findInstanceOf($ast, Namespace_::class);

            if (count($classes) === 0) {
                return false;
            }
        } catch (Throwable) {
            Assert::fail("Unable to parse file: {$file->getPathname()}");
        }

        return true;
    }
}
