<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions;

use Jpeters8889\PhpUnitCodeAssertions\Builders\ClassesInAssertableBuilder;
use Jpeters8889\PhpUnitCodeAssertions\Builders\CodeAssertableBuilder;
use PHPUnit\Framework\TestCase;

class CodeAssertionsTestCase extends TestCase
{
    public function assertCodeIn(string $path = './app'): CodeAssertableBuilder
    {
        return new CodeAssertableBuilder($path);
    }

    public function assertClassesIn(string $pathOrNamespace): ClassesInAssertableBuilder
    {
        return new ClassesInAssertableBuilder($pathOrNamespace);
    }
}
