<?php

declare(strict_types=1);

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
use Error;

abstract class AssertableBuilder
{
    use GetsAbsolutePath;
    use RetrievesFiles;

    /** @var Collection<int, PendingAssertion> */
    protected Collection $assertionsToMake;

    protected bool $hasExecutedAssertions = false;

    protected string $absolutePath;

    protected string $localPath;

    protected array $aliases;

    public function __construct(string $pathOrNamespace)
    {
        $this->normalisePath($pathOrNamespace);

        $this->assertionsToMake = collect();
        $this->aliases = $this->methodAliases();
    }

    protected function normalisePath(string $pathOrNamespace): void
    {
        if (str_contains($pathOrNamespace, '\\')) {
            $this->resolvePathsFromNamespace($pathOrNamespace);

            return;
        }

        $this->localPath = $pathOrNamespace;
        $this->absolutePath = $this->getAbsolutePath($this->localPath);
    }

    protected function resolvePathsFromNamespace(string $pathOrNamespace): void
    {
        $basePath = $this->getAbsolutePath('');
        $rootNamespace = Str::of($pathOrNamespace)->before('\\')->append('\\')->toString();

        /** @var Collection<string, string> $loadedClasses */
        /** @phpstan-ignore-next-line  */
        $loadedClasses = collect(include $basePath . 'vendor/composer/autoload_psr4.php');

        $path = $loadedClasses
            ->filter(fn ($path, $namespace) => Str::startsWith($namespace, $rootNamespace) && Str::contains($pathOrNamespace, $namespace))
            ->first();

        $pathOrNamespace = Str::of($path[0])
            ->append('/')
            ->when(
                $rootNamespace === 'App',
                fn (Stringable $string) => $string->append(Str::of($pathOrNamespace)->after($rootNamespace)->toString()),
                fn (Stringable $string) => $string->append(Str::of($pathOrNamespace)->after($rootNamespace)->after('\\')->toString()),
            )
            ->replace('\\', '/')
            ->toString();

        $this->absolutePath = $pathOrNamespace;
        $this->localPath = Str::of($pathOrNamespace)->after($basePath)->toString();
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
            ->map(fn (SplFileInfo $file) => new PendingFile(
                fileName: $file->getFilename(),
                localPath: Str::of($file->getPathname())->after($this->absolutePath)->ltrim('/')->toString(),
                absolutePath: $file->getPathname(),
                contents: $file->getContents(),
            ))
            ->values();
    }

    /**
     * @param class-string<Assertable> $assertion
     * @param array<mixed> $args
     */
    public function addAssertion(string $assertion, bool $negate = false, array $args = []): static
    {
        $this->assertionsToMake->push(new PendingAssertion($assertion, $negate, $args));

        return $this;
    }

    /**
     * @param string|class-string|array<string|class-string> $fqns
     */
    public function except(string|array $fqns): static
    {
        /** @var PendingAssertion | null $mostRecentAssertion */
        $mostRecentAssertion = $this->assertionsToMake->pop();

        if ( ! $mostRecentAssertion) {
            Assert::fail("Can't chain except without an assertion");
        }

        $this->assertionsToMake->push(new PendingAssertion(
            $mostRecentAssertion->assertable,
            $mostRecentAssertion->negate,
            $mostRecentAssertion->args,
            except: is_array($fqns) ? $fqns : [$fqns],
        ));

        return $this;
    }

    /** @return Collection<int, PendingAssertion> */
    public function getAssertionsToMake(): Collection
    {
        return $this->assertionsToMake;
    }

    /**
     * @return array<string, string> | array<string, array<string, callable>>
     */
    public function methodAliases(): array
    {
        return [];
    }

    public function __call(string $method, array $args)
    {
        if ( ! array_key_exists($method, $this->aliases)) {
            throw new Error('Call to undefined method ' . get_class($this) . '::' . $method);
        }

        $newMethod = $this->aliases[$method];

        if (is_array($newMethod)) {
            $args = call_user_func($newMethod[1], ...$args);
            $newMethod = $newMethod[0];
        }

        return $this->$newMethod(...$args);
    }

    public function executeAssertions(): void
    {
        $this->collectFilesToAssertAgainst()->each(function (PendingFile $file): void {
            $this->assertionsToMake
                ->map(fn (PendingAssertion $pendingAssertion) => AssertableFactory::make($pendingAssertion->assertable, $pendingAssertion->args))
                ->each(fn (Assertable $assertion, int $index) => $assertion->assert($file, $this->assertionsToMake[$index]->negate, $this->assertionsToMake[$index]->except));
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
