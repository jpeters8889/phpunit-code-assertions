<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UsesFunctionsTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAFileDoesNotContainsAFunction(): void
    {
        $assertion = new UsesFunctions(['foo']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that a file uses functions,\ntests/Fixtures/MyClass.php does not use function foo()");

        $assertion->assert($this->pendingFileFactory(), false, []);
    }

    #[Test]
    public function itCanDetectWhenAFileDoesContainsAFunction(): void
    {
        $assertion = new UsesFunctions(['ucwords']);

        $assertion->assert($this->pendingFileFactory(), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itCanDetectWhenAFileContainsAForbiddenFunctionWhenNegatingTheAssert(): void
    {
        $assertion = new UsesFunctions(['ucwords']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that a file does not use functions,\ntests/Fixtures/MyClass.php uses function ucwords()");

        $assertion->assert($this->pendingFileFactory(), true, []);
    }

    #[Test]
    public function itCanDetectWhenAFileContainsMultipleForbiddenFunctionWhenNegatingTheAssert(): void
    {
        $assertion = new UsesFunctions(['ucwords', 'lcfirst']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that a file does not use functions,\ntests/Fixtures/MyClass.php uses function ucwords()\ntests/Fixtures/MyClass.php uses function lcfirst()");

        $assertion->assert($this->pendingFileFactory(), true, []);
    }

    #[Test]
    public function itDoesntErrorIfAClassDoesNotUseAnForbiddenFunctionWhenNegatingTheAssert(): void
    {
        $assertion = new UsesFunctions(['assert']);

        $assertion->assert($this->pendingFileFactory(), true, []);

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
