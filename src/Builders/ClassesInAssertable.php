<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreInterfaces;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToImplement;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToUse;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\Expr\Error;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;
use Symfony\Component\Finder\SplFileInfo;

class ClassesInAssertable extends CodeAssertable
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

    public function toExtend()
    {

    }

    public function toNotExtend()
    {

    }

    public function toHaveMethod()
    {

    }

    public function toNotHaveMethod()
    {

    }

    public function toHaveSuffix()
    {

    }

    public function toNotHaveSuffix()
    {

    }

    public function toBeInvokable()
    {

    }

    public function toBeNotInvokable()
    {

    }

    public function toOnlyHaveMethod()
    {

    }

    public function toNotOnlyHaveMethod()
    {

    }

    public function toBeFinal()
    {

    }

    public function toBeNotFinal()
    {

    }

    public function toUseStrictTypes()
    {

    }

    public function toNotUseStrictTypes()
    {

    }

    protected function isFileTestable(SplFileInfo $file): bool
    {
        try {
            $ast = PhpFileParser::parse($file->getContents());

            $classes = (new NodeFinder())->findInstanceOf($ast, Namespace_::class);

            if(count($classes) === 0) {
                return false;
            }
        } catch (Error) {
            Assert::fail("Unable to parse file: {$file->getPathname()}");
        }

        return true;
    }


}
