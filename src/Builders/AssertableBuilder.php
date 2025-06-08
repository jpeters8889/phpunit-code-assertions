<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingAssertion;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Factories\AssertableFactory;
use PHPUnit\Framework\Assert;
use Symfony\Component\Finder\SplFileInfo;

abstract class AssertableBuilder
{
    use GetsAbsolutePath;
    use RetrievesFiles;

    /** @var Collection<int, PendingAssertion> */
    protected Collection $assertionsToMake;

    protected bool $hasExecutedAssertions = false;

    protected string $absolutePath;

    protected string $localPath;

    public function __construct(string $pathOrNamespace)
    {
        $this->normalisePath($pathOrNamespace);

        $this->assertionsToMake = collect();
    }

    protected function normalisePath($pathOrNamespace): void
    {
        if (str_contains($pathOrNamespace, '\\')) {
            $this->resolvePathsFromNamespace($pathOrNamespace);

            return;
        }

        $this->localPath = $pathOrNamespace;
        $this->absolutePath = $this->getAbsolutePath($this->localPath);
    }

    protected function resolvePathsFromNamespace($pathOrNamespace): void
    {
        $basePath = $this->getAbsolutePath('');
        $rootNamespace = Str::of($pathOrNamespace)->before('\\')->append('\\');

        $path = collect(include $basePath . 'vendor/composer/autoload_psr4.php')
            ->filter(function ($path, $namespace) use ($rootNamespace, $pathOrNamespace) {
                return Str::startsWith($namespace, $rootNamespace) && Str::contains($pathOrNamespace, $namespace);
            })
            ->first();

        $pathOrNamespace = Str::of($path[0])
            ->append('/')
            ->when(
                $rootNamespace === 'App',
                fn(Stringable $string) => $string->append(Str::of($pathOrNamespace)->after($rootNamespace)),
                fn(Stringable $string) => $string->append(Str::of($pathOrNamespace)->after($rootNamespace)->after('\\')),
            )
            ->replace('\\', '/')
            ->toString();

        $this->absolutePath = $pathOrNamespace;
        $this->localPath = Str::of($pathOrNamespace)->after($basePath);
    }

    protected function isFileTestable(SplFileInfo $file): bool
    {
        return true;
    }

    /** @return Collection<int, PendingFile> */
    protected function collectFilesToAssertAgainst(): Collection
    {
        return collect($this->getFiles($this->absolutePath)->name('*.php')->getIterator())
            ->filter($this->isFileTestable(...))
            ->map(fn(SplFileInfo $file) => new PendingFile(
                fileName: $file->getFilename(),
                localPath: Str::of($file->getPathname())->after($this->absolutePath)->ltrim('/'),
                absolutePath: $file->getPathname(),
                contents: $file->getContents(),
                fqns: null,
            ));
    }

    /** @param class-string<Assertable> $assertion */
    public function addAssertion(string $assertion, bool $negate = false, array $args = []): self
    {
        $this->assertionsToMake->push(new PendingAssertion($assertion, $negate, $args));

        return $this;
    }

    public function except(string|array $fqns): self
    {
        /** @var ?PendingAssertion $mostRecentAssetion */
        $mostRecentAssertion = $this->assertionsToMake->last();

        if(!$mostRecentAssertion) {
            Assert::fail("Can't us except without an assertion");
        }

        $this->assertionsToMake->pop();

        $this->assertionsToMake->push(new PendingAssertion(
            $mostRecentAssertion->assertable,
            $mostRecentAssertion->negate,
            $mostRecentAssertion->args,
            except: is_array($fqns) ? $fqns : [$fqns],
        ));

        return $this;
    }

    public function getAssertionsToMake(): Collection
    {
        return $this->assertionsToMake;
    }

    public function executeAssertions(): void
    {
        $this->collectFilesToAssertAgainst()->each(function(PendingFile $file) {
            $this->assertionsToMake
                ->map(fn(PendingAssertion $pendingAssertion) => AssertableFactory::make($pendingAssertion->assertable, $pendingAssertion->args))
                ->each(fn(Assertable $assertion, int $index) => $assertion->assert($file, $this->assertionsToMake[$index]->negate, $this->assertionsToMake[$index]->except));
        });

        $this->hasExecutedAssertions = true;
    }

    public function __destruct()
    {
        if ($this->hasExecutedAssertions) {
            return;
        }

        $this->executeAssertions();
    }
}
