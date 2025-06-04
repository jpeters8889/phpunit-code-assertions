<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Assertions;

use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;

class AreInterfaces implements Assertable
{
    public function assert(PendingFile $file, bool $negate = false): void
    {
        $ast = PhpFileParser::parse($file->contents);

        collect((new NodeFinder())->findInstanceOf($ast, Interface_::class))
            ->when(
                !$negate,
                fn(Collection $nodes) => $nodes->whenEmpty(fn() => Assert::fail("{$file->localPath} is not an interface")),
                fn(Collection $nodes) => $nodes->whenNotEmpty(fn() => Assert::fail("{$file->localPath} is an interface")),
            );
    }
}
