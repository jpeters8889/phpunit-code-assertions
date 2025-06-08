<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Assertions;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\FileUsesFunction;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;

class OnlyHaveMethod implements Assertable
{
    public function __construct(protected string $method)
    {
    }

    public function assert(PendingFile $file, bool $negate = false, array $except = []): void
    {
        $ast = PhpFileParser::parse($file->contents);

        $namespaceNode = (new NodeFinder())->findFirstInstanceOf($ast, Namespace_::class);

        /** @var Class_ $class */
        $class = Arr::first((new NodeFinder())->findInstanceOf($ast, Class_::class));

        if(in_array($namespaceNode->name->toString().'\\'.$class->name->toString(), $except, true)) {
            Assert::assertTrue(true);

            return;
        }

        $methods = collect($class->getMethods());

        if($methods->count() !== 1 && !$negate) {
            Assert::fail("{$file->localPath} does not have exactly one method");
        }

        $methodName = $methods->first()->name->toString();

        if($methodName !== $this->method) {
            if($negate) {
                Assert::fail("{$file->localPath} has method {$this->method}");
            }

            Assert::fail("{$file->localPath} does not have method {$this->method}");
        }

        if ($negate){
            Assert::fail("{$file->localPath} only contains method {$this->method}");
        }

        Assert::assertTrue(true);
    }
}
