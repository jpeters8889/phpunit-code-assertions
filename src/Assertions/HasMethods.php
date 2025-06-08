<?php

declare(strict_types=1);

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

class HasMethods implements Assertable
{
    /** @var Collection<int, FileUsesFunction> */
    protected Collection $failures;

    /** @param string[] $methods */
    public function __construct(protected array $methods)
    {
        $this->failures = collect();
    }

    public function assert(PendingFile $file, bool $negate = false, array $except = []): void
    {
        $matches = collect($this->methods)->mapWithKeys(fn ($method) => [$method => false])->toArray();

        $ast = PhpFileParser::parse($file->contents);

        $namespaceNode = (new NodeFinder())->findFirstInstanceOf($ast, Namespace_::class);

        /** @var Class_ $class */
        $class = Arr::first((new NodeFinder())->findInstanceOf($ast, Class_::class));

        if (in_array($namespaceNode->name->toString() . '\\' . $class->name->toString(), $except, true)) {
            Assert::assertTrue(true);

            return;
        }

        collect($class->getMethods())->each(function (ClassMethod $method) use (&$matches): void {
            $matches[$method->name->toString()] = true;
        });

        collect($matches)
            ->reject(fn ($match) => $match === $negate ? false : true)
            ->whenNotEmpty(
                fn (Collection $matches) => $matches
                    ->each(fn ($match, $method) => $this->failures->push(new FileUsesFunction($file->localPath, $method)))
            );

        if ($this->failures->isNotEmpty()) {
            Assert::fail($this->failureMessage($negate));
        }

        Assert::assertTrue(true);
    }

    protected function failureMessage(bool $negated): string
    {
        return $this->failures
            ->unique(fn (FileUsesFunction $fileUsesFunction) => $fileUsesFunction->filePath . $fileUsesFunction->functionName)
            ->when(
                $negated,
                fn (Collection $failures) => $failures
                    ->map(fn (FileUsesFunction $fileUsesFunction) => "{$fileUsesFunction->filePath} has method {$fileUsesFunction->functionName}()")
                    ->prepend("Failed asserting that a class does not have methods,"),
                fn (Collection $failures) => $failures
                    ->map(fn (FileUsesFunction $fileUsesFunction) => "{$fileUsesFunction->filePath} does not have method {$fileUsesFunction->functionName}()")
                    ->prepend("Failed asserting that a class has methods,"),
            )
            ->join("\n");
    }

}
