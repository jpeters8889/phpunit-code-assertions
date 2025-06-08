<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Concerns;

use Symfony\Component\Finder\Finder;

trait RetrievesFiles
{
    protected function getFiles(string $path): Finder
    {
        $finder = new Finder();

        $finder->files()->in($path);

        return $finder;
    }
}
