<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Assertions;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\FileUsesFunction;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;

class UsesFunctions implements Assertable
{
    use RetrievesFiles;

    /** @var Collection<int, FileUsesFunction> */
    protected Collection $failures;

    /** @param string[] $functions */
    public function __construct(protected array $functions)
    {
        $this->failures = collect();
    }

    public function assert(PendingFile $file, bool $negate = false, array $except = []): void
    {
        $this->scanFileForMethodUsage($file, $negate, $except);

        if ($this->failures->isNotEmpty()) {
            Assert::fail($this->failureMessage($negate));
        }

        Assert::assertTrue(true);
    }

    /** @param array<string|class-string> $except */
    protected function scanFileForMethodUsage(PendingFile $file, bool $negate, array $except): void
    {
        $ast = PhpFileParser::parse($file->contents);

        $namespaceNode = (new NodeFinder())->findFirstInstanceOf($ast, Namespace_::class);

        $class = Arr::first((new NodeFinder())->findInstanceOf($ast, Class_::class));

        if ($class && $namespaceNode && in_array($namespaceNode->name->toString() . '\\' . $class->name->toString(), $except, true)) {
            Assert::assertTrue(true);

            return;
        }

        if (in_array($file->fileName, $except, true)) {
            Assert::assertTrue(true);

            return;
        }

        $matches = collect($this->functions)->mapWithKeys(fn ($method) => [$method => false])->toArray();

        collect((new NodeFinder())->findInstanceOf($ast, FuncCall::class))
            ->filter(fn (FuncCall $call) => $call->name instanceof Name)
            ->each(function (FuncCall $call) use ($file, &$matches, $negate): void {
                if ($negate) {
                    if (in_array($call->name->toString(), $this->functions, true)) {
                        $this->failures->push(new FileUsesFunction($file->localPath, $call->name->toString()));
                    }

                    return;
                }

                if (in_array($call->name->toString(), $this->functions, true)) {
                    $matches[$call->name->toString()] = true;
                }

                collect($matches)
                    ->reject(fn ($match) => $match === true)
                    ->whenNotEmpty(
                        fn (Collection $matches) => $matches
                            ->each(fn ($match, $method) => $this->failures->push(new FileUsesFunction($file->localPath, $method)))
                    );
            });
    }

    protected function failureMessage(bool $negated): string
    {
        return $this->failures
            ->unique(fn (FileUsesFunction $fileUsesFunction) => $fileUsesFunction->filePath . $fileUsesFunction->functionName)
            ->when(
                $negated,
                fn (Collection $failures) => $failures
                    ->map(fn (FileUsesFunction $fileUsesFunction) => "{$fileUsesFunction->filePath} uses function {$fileUsesFunction->functionName}()")
                    ->prepend("Failed asserting that a file does not use functions,"),
                fn (Collection $failures) => $failures
                    ->map(fn (FileUsesFunction $fileUsesFunction) => "{$fileUsesFunction->filePath} does not use function {$fileUsesFunction->functionName}()")
                    ->prepend("Failed asserting that a file uses functions,"),
            )
            ->join("\n");
    }
}
