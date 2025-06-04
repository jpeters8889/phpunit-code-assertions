<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Assertions;

use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;

class ToUse implements Assertable
{
    public function __construct(protected string $trait)
    {
        //
    }

    public function assert(PendingFile $file, bool $negate = false): void
    {
        $ast = PhpFileParser::parse($file->contents);

        $uses = collect((new NodeFinder())->findInstanceOf($ast, Use_::class))
            ->map(fn(Use_ $foo) => collect($foo->uses)->map(fn(UseItem $use) => $use->name->name))
            ->flatten();

        collect((new NodeFinder())->findInstanceOf($ast, Class_::class))
            ->filter(fn() => $uses->contains($this->trait))
            ->filter(fn(Class_ $class) => collect($class->getTraitUses())
                ->map(fn(TraitUse $traits) => collect($traits->traits)->map(fn(Name $trait) => $trait->name))
                ->flatten()
                ->contains(class_basename($this->trait)))
            ->when(
                !$negate,
                fn(Collection $nodes) => $nodes->whenEmpty(fn() => Assert::fail("{$file->localPath} does not use {$this->trait}")),
                fn(Collection $nodes) => $nodes->whenNotEmpty(fn() => Assert::fail("{$file->localPath} uses {$this->trait}")),
            );
    }
}
