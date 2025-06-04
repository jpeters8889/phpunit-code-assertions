<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Assertions;

use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\FileUsesFunction;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\PhpFileParser;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\NodeFinder;
use PHPUnit\Framework\Assert;

class UsesFunctions implements Assertable
{
    use RetrievesFiles;

    protected Collection $failures;

    public function __construct(protected array $methods)
    {
        $this->failures = collect();
    }

    public function assert(PendingFile $file, bool $negate = false): void
    {
        $this->scanFileForMethodUsage($file, $negate);

        if ($this->failures->isNotEmpty()) {
            Assert::fail($this->failureMessage($negate));
        }
    }

    protected function scanFileForMethodUsage(PendingFile $file, bool $negate): void
    {
        $ast = PhpFileParser::parse($file->contents);

        $matches = collect($this->methods)->mapWithKeys(fn($method) => [$method => false])->toArray();

        collect((new NodeFinder())->findInstanceOf($ast, FuncCall::class))
            ->filter(fn(FuncCall $call) => $call->name instanceof Name)
            ->each(function (FuncCall $call) use ($file, &$matches, $negate) {
                if($negate) {
                    if (in_array($call->name->toString(), $this->methods, true)) {
                        $this->failures->push(new FileUsesFunction($file->localPath, $call->name->toString()));
                    }

                    return;
                }

                if (in_array($call->name->toString(), $this->methods, true)) {
                    $matches[$call->name->toString()] = true;
                }

                collect($matches)
                    ->reject(fn($match) => $match === true)
                    ->whenNotEmpty(fn(Collection $matches) => $matches
                        ->each(fn($match, $method) => $this->failures->push(new FileUsesFunction($file->localPath, $method)))
                    );
            });
    }

    protected function failureMessage(string $negated): string
    {
        return $this->failures
            ->unique(fn(FileUsesFunction $fileUsesFunction) => $fileUsesFunction->filePath.$fileUsesFunction->functionName)
            ->when(
                $negated,
                fn(Collection $failures) => $failures
                    ->map(fn(FileUsesFunction $fileUsesFunction) => "{$fileUsesFunction->filePath} uses function {$fileUsesFunction->functionName}()")
                    ->prepend("Failed asserting that a file does not use functions,"),
                fn(Collection $failures) => $failures
                    ->map(fn(FileUsesFunction $fileUsesFunction) => "{$fileUsesFunction->filePath} does not use function {$fileUsesFunction->functionName}()")
                    ->prepend("Failed asserting that a file uses functions,"),
            )
            ->join("\n");
    }
}
