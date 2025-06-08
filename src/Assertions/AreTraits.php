<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Assertions;

use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;

class AreTraits implements Assertable
{
    public function assert(PendingFile $file, bool $negate = false, array $except = []): void
    {
        $ast = PhpFileParser::parse($file->contents);

        $namespaceNode = (new NodeFinder())->findFirstInstanceOf($ast, Namespace_::class);

        collect((new NodeFinder())->findInstanceOf($ast, Trait_::class))
            ->reject(fn(Trait_ $trait) => in_array($namespaceNode->name->toString().'\\'.$trait->name->toString(), $except, true))
            ->when(fn(Collection $collection) => ($collection->isNotEmpty() && count($except) > 0) || count($except) === 0, fn(Collection $collection) => $collection
            ->when(
                !$negate,
                fn(Collection $nodes) => $nodes->whenEmpty(fn() => Assert::fail("{$file->localPath} is not a trait")),
                fn(Collection $nodes) => $nodes->whenNotEmpty(fn() => Assert::fail("{$file->localPath} is a trait")),
            )
            );

        Assert::assertTrue(true);
    }
}
