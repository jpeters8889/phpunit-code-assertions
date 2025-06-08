<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\IsReadOnly;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\ReadOnlyClass\MyClass;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IsReadOnlyTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassIsntReadOnly(): void
    {
        $assertion = new IsReadOnly();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/BaseClass/MyClass is not a read only class");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenANonReadOnlyClassIsDetetectedButIsExcluded(): void
    {
        $assertion = new IsReadOnly();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, [\Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\BaseClass\MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassIsReadOnly(): void
    {
        $assertion = new IsReadOnly();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/ReadOnlyClass/MyClass.php',
            absolutePath: 'tests/Fixtures/ReadOnlyClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/ReadOnlyClass/MyClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClassIsReadOnlyWhenItShouldntBe(): void
    {
        $assertion = new IsReadOnly();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/ReadOnlyClass/MyClass.php is a read only class");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/ReadOnlyClass/MyClass.php',
            absolutePath: 'tests/Fixtures/ReadOnlyClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/ReadOnlyClass/MyClass.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAClassIsExcluded(): void
    {
        $assertion = new IsReadOnly();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/ReadOnlyClass/MyClass.php',
            absolutePath: 'tests/Fixtures/ReadOnlyClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/ReadOnlyClass/MyClass.php'))
        ), negate: true, except: [MyClass::class]);

        $this->assertTrue(true);
    }
}
