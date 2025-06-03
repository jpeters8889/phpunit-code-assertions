<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Composer\Autoload\ClassLoader;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\DoesNotUseFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DoesNotUseFunctionsTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAFileContainsAForbiddenFunction(): void
    {
        $assertion = new DoesNotUseFunctions(['ucwords']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that a file does not use functions,\ntests/Fixtures/MyClass.php uses function ucwords()");

        $assertion->assert($this->pendingFileFactory());
    }

    #[Test]
    public function itCanDetectWhenAFileContainsMultipleForbiddenFunction(): void
    {
        $assertion = new DoesNotUseFunctions(['ucwords', 'lcfirst']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that a file does not use functions,\ntests/Fixtures/MyClass.php uses function ucwords()\ntests/Fixtures/MyClass.php uses function lcfirst()");

        $assertion->assert($this->pendingFileFactory());
    }

    #[Test]
    public function itDoesntErrorIfAClassDoesNotUseAnForbiddenFunction(): void
    {
        $assertion = new DoesNotUseFunctions(['assert']);

        $assertion->assert($this->pendingFileFactory());

        $this->assertTrue(true);
    }

    protected function pendingFileFactory(): PendingFile
    {
        return new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        );
    }
}
