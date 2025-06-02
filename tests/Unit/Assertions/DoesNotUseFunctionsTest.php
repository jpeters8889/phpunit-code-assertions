<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Composer\Autoload\ClassLoader;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\DoesNotUseFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DoesNotUseFunctionsTest extends TestCase
{
    use RetrievesFiles;

    #[Test]
    public function itCanDetectWhenAFileContainsAForbiddenFunction(): void
    {
        $assertion = new DoesNotUseFunctions($this->getAbsolutePath('tests/Fixtures'), 'tests/Fixtures', ['ucwords']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that path does not use functions\ntests/Fixtures/MyClass.php uses function ucwords()");

        $assertion->assert();
    }

    #[Test]
    public function itCanDetectWhenAFileContainsMultipleForbiddenFunction(): void
    {
        $assertion = new DoesNotUseFunctions($this->getAbsolutePath('tests/Fixtures'), 'tests/Fixtures', ['ucwords', 'lcfirst']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that path does not use functions\ntests/Fixtures/MyClass.php uses function ucwords()\ntests/Fixtures/MyClass.php uses function lcfirst()");

        $assertion->assert();
    }

    #[Test]
    public function itDoesntErrorIfAClassDoesNotUseAnForbiddenFunction(): void
    {
        $assertion = new DoesNotUseFunctions($this->getAbsolutePath('tests/Fixtures'), 'tests/Fixtures', ['assert']);

        $assertion->assert();

        $this->assertTrue(true);
    }
}
