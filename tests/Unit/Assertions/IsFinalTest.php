<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\IsFinal;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\FinalClass\MyClass;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IsFinalTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassIsntFinal(): void
    {
        $assertion = new IsFinal();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/BaseClass/MyClass is not an final class");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenANonFinaClassIsDetectedButExcluded(): void
    {
        $assertion = new IsFinal();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, [\Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\BaseClass\MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassIsFinal(): void
    {
        $assertion = new IsFinal();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/FinalClass/MyClass.php',
            absolutePath: 'tests/Fixtures/FinalClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/FinalClass/MyClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClassIsFinalWhenItShouldntBe(): void
    {
        $assertion = new IsFinal();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/FinalClass/MyClass.php is an final class");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/FinalClass/MyClass.php',
            absolutePath: 'tests/Fixtures/FinalClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/FinalClass/MyClass.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAFailingClassIsExcluded(): void
    {
        $assertion = new IsFinal();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/FinalClass/MyClass.php',
            absolutePath: 'tests/Fixtures/FinalClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/FinalClass/MyClass.php'))
        ), negate: true, except: [MyClass::class]);

        $this->assertTrue(true);
    }
}
