<?php

namespace Jpeters8889\PhpUnitCodeAssertions;

use Jpeters8889\PhpUnitCodeAssertions\Builders\CodeAssertable;
use PHPUnit\Framework\TestCase;

class CodeAssertionsTestCase extends TestCase
{
    public function assertCode(string $path = './app'): CodeAssertable
    {
        return new CodeAssertable($path);
    }
}
