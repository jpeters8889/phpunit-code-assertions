<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Assertions;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\DeclareItem;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;

class HasStrictTypes implements Assertable
{
    public function assert(PendingFile $file, bool $negate = false, array $except = []): void
    {
        $ast = PhpFileParser::parse($file->contents);

        $namespaceNode = (new NodeFinder())->findFirstInstanceOf($ast, Namespace_::class);

        /** @var Class_ $class */
        $class = Arr::first((new NodeFinder())->findInstanceOf($ast, Class_::class));

        if($class && $namespaceNode && in_array($namespaceNode->name->toString().'\\'.$class->name->toString(), $except, true)) {
            Assert::assertTrue(true);

            return;
        }

        if(in_array($file->fileName, $except, true)) {
            Assert::assertTrue(true);

            return;
        }

        $declarations = collect((new NodeFinder())->findInstanceOf($ast, Declare_::class))
            ->map(fn(Declare_ $declare) => collect($declare->declares)
                ->mapWithKeys(fn(DeclareItem $declaration) => [$declaration->key->name => $declaration->value->value])
                ->filter()
            )
            ->first();

        $strictTypes = Arr::get($declarations, 'strict_types');

        if(!$strictTypes && !$negate) {
            Assert::fail("{$file->localPath} does not declare strict types.");
        }

        if($strictTypes && $negate) {
            Assert::fail("{$file->localPath} declares strict types.");
        }

        Assert::assertTrue(true);
    }
}
