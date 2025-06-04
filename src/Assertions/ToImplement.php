<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Assertions;

use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;

class ToImplement implements Assertable
{
    public function __construct(protected string $interface)
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
            ->filter(fn() => $uses->contains($this->interface))
            ->filter(fn(Class_ $class) => collect($class->implements)
                ->map(fn(Name $interface) => $interface->name)
                ->contains(class_basename($this->interface)))
            ->when(
                !$negate,
                fn(Collection $nodes) => $nodes->whenEmpty(fn() => Assert::fail("{$file->localPath} does not implement {$this->interface}")),
                fn(Collection $nodes) => $nodes->whenNotEmpty(fn() => Assert::fail("{$file->localPath} implements {$this->interface}")),
            );
    }
}
