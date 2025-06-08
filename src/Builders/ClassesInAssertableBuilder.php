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

class ClassesInAssertableBuilder extends CodeAssertableBuilder
{
    public function areTraits(): self
    {
        return $this->addAssertion(AreTraits::class);
    }

    public function areNotTraits(): self
    {
        return $this->addAssertion(AreTraits::class, negate: true);
    }

    public function areClasses(): self
    {
        return $this->addAssertion(AreClasses::class);
    }

    public function areNotClasses(): self
    {
        return $this->addAssertion(AreClasses::class, negate: true);
    }

    public function areInterfaces(): self
    {
        return $this->addAssertion(AreInterfaces::class);
    }

    public function areNotInterfaces(): self
    {
        return $this->addAssertion(AreInterfaces::class, negate: true);
    }

    public function areContracts(): self
    {
        return $this->areInterfaces();
    }

    public function areNotContracts(): self
    {
        return $this->areNotInterfaces();
    }

    public function toImplement(string $interface): self
    {
        return $this->addAssertion(ToImplement::class, args: [$interface]);
    }

    public function toNotImplement(string $interface): self
    {
        return $this->addAssertion(ToImplement::class, negate: true, args: [$interface]);
    }

    public function toUse(string $trait): self
    {
        return $this->addAssertion(ToUse::class, args: [$trait]);
    }

    public function toNotUse(string $trait): self
    {
        return $this->addAssertion(ToUse::class, negate: true, args: [$trait]);
    }

    public function toExtend(string $class): self
    {
        return $this->addAssertion(ToExtend::class, args: [$class]);
    }

    public function toNotExtend(string $class): self
    {
        return $this->addAssertion(ToExtend::class, negate: true, args: [$class]);
    }

    public function toBeAbstract(): self
    {
        return $this->addAssertion(IsAbstract::class);
    }

    public function toNotBeAbstract(): self
    {
        return $this->addAssertion(IsAbstract::class, negate: true);
    }

    public function toHaveMethods(array $methods): self
    {
        return $this->addAssertion(HasMethods::class, args: $methods);
    }

    public function toNotHaveMethods(array $methods): self
    {
        return $this->addAssertion(HasMethods::class, negate: true, args: $methods);
    }

    public function toHaveMethod(string $method): self
    {
        return $this->toHaveMethods([$method]);
    }

    public function toNotHaveMethod(string $method): self
    {
        return $this->toNotHaveMethods([$method]);
    }

    public function toBeInvokable(): self
    {
        return $this->toHaveMethod('__invoke');
    }

    public function toBeNotInvokable(): self
    {
        return $this->toNotHaveMethod('__invoke');
    }

    public function toOnlyHaveMethod(string $method): self
    {
        return $this->addAssertion(OnlyHaveMethod::class, args: [$method]);
    }

    public function toNotOnlyHaveMethod(string $method): self
    {
        return $this->addAssertion(OnlyHaveMethod::class, negate: true, args: [$method]);
    }

    public function toOnlyBeInvokable()
    {
        return $this->toOnlyHaveMethod('__invoke');
    }

    public function toNotOnlyBeInvokable()
    {
        return $this->toNotOnlyHaveMethod('__invoke');
    }

    public function toBeFinal(): self
    {
        return $this->addAssertion(IsFinal::class);
    }

    public function toBeNotFinal(): self
    {
        return $this->addAssertion(IsFinal::class, negate: true);
    }

    public function toBeReadOnly(): self
    {
        return $this->addAssertion(IsReadOnly::class);
    }

    public function toBeNotReadOnly(): self
    {
        return $this->addAssertion(IsReadOnly::class, negate: true);
    }

    public function toHaveSuffix(string $suffix): self
    {
        return $this->addAssertion(HasSuffix::class, args: [$suffix]);
    }

    public function toNotHaveSuffix(string $suffix): self
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
        } catch (Error) {
            Assert::fail("Unable to parse file: {$file->getPathname()}");
        }

        return true;
    }
}
