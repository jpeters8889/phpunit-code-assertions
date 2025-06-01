<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Assertions;

use Illuminate\Support\Collection;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\FileUsesFunction;
use PHPUnit\Framework\Assert;

class DoesNotUseFunctions implements Assertable
{
    use RetrievesFiles;

    protected Collection $failures;

    public function __construct(protected string $path, protected array $methods)
    {
        $this->failures = collect();
    }

    public function assert(): void
    {
        foreach($this->getFiles($this->path)->name('*.php') as $file) {
            $this->scanFileForMethodUsage($file->getContents(), $file);
        }

        if ($this->failures->isNotEmpty()) {
            Assert::fail($this->failureMessage());
        }
    }

    protected function scanFileForMethodUsage(string $content, mixed $file): void
    {
        collect($this->methods)
            ->filter(fn(string $method) => preg_grep("/\b{$method}\(/i", [$content]))
            ->each(fn(string $method) => $this->failures->push(new FileUsesFunction($file->getPathname(), $method)));
    }

    protected function failureMessage(): string
    {
        return $this->failures
            ->map(fn(FileUsesFunction $fileUsesMethod) => "{$fileUsesMethod->filePath} uses function {$fileUsesMethod->functionName}()")
            ->prepend('Failed asserting that path does not use functions')
            ->join("\n");
    }
}
