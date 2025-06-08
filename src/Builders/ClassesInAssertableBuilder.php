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
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

/**
 * @method self implement(string $interface)
 * @method self implements(string $interface)
 * @method self areContracts()
 * @method self areNotContracts()
 * @method self uses(string $trait)
 * @method self extends(string $class)
 * @method self isAbstract()
 * @method self hasMethods(array $methods)
 * @method self toHaveMethod(string $method)
 * @method self hasMethod(string $method)
 * @method self toNotHaveMethod(string $method)
 * @method self toBeInvokable()
 * @method self toNotBeInvokable()
 * @method self isInvokable()
 * @method self toBeOnlyInvokable()
 * @method self toNotOnlyBeInvokable()
 * @method self isFinal()
 * @method self areReadOnly()
 * @method self hasSuffix(string $suffix)
 */
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

    public function toOnlyHaveMethod(string $method): static
    {
        return $this->addAssertion(OnlyHaveMethod::class, args: [$method]);
    }

    public function toNotOnlyHaveMethod(string $method): static
    {
        return $this->addAssertion(OnlyHaveMethod::class, negate: true, args: [$method]);
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

    public function methodAliases(): array
    {
        return [
            'implement' => 'toImplement',
            'implements' => 'toImplement',
            'areContracts' => 'areInterfaces',
            'areNotContracts' => 'areNotInterfaces',
            'uses' => 'toUse',
            'extends' => 'toExtend',
            'isAbstract' => 'toBeAbstract',
            'hasMethods' => 'toHaveMethods',
            'toHaveMethod' => ['toHaveMethods', static fn (string $method) => [[[$method]]]],
            'hasMethod' => ['toHaveMethods', static fn (string $method) => [[[$method]]]],
            'toNotHaveMethod' => ['toNotHaveMethods', static fn (string $method) => [[[$method]]]],
            'toBeInvokable' => ['toHaveMethods', static fn () => [[['__invoke']]]],
            'toNotBeInvokable' => ['toNotHaveMethods', static fn () => [[['__invoke']]]],
            'isInvokable' => ['toHaveMethods', static fn () => [[['__invoke']]]],
            'toBeOnlyInvokable' => ['toOnlyHaveMethod', static fn () => ['__invoke']],
            'toNotOnlyBeInvokable' => ['toNotOnlyHaveMethod', static fn () => ['__invoke']],
            'isFinal' => 'toBeFinal',
            'areReadOnly' => 'toBeReadOnly',
            'hasSuffix' => 'toHaveSuffix',
        ];
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
