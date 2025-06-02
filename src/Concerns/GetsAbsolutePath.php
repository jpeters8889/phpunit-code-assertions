<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Concerns;

use Composer\Autoload\ClassLoader;

trait GetsAbsolutePath
{
    protected function getAbsolutePath(string $path): string
    {
        $basePath = dirname(array_values(array_filter(
            array_keys(ClassLoader::getRegisteredLoaders()),
            fn ($path) => ! str_starts_with($path, 'phar://'),
        ))[0]);

        return $basePath . '/' . $path;
    }
}
