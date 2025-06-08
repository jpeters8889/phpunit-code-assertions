<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MyClass;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AreClassesTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassIsntAClass(): void
    {
        $assertion = new AreClasses();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/Traits/MyTrait.php is not a class");

        $assertion->assert(new PendingFile(
            fileName: 'MyTrait.php',
            localPath: 'tests/Fixtures/Traits/MyTrait.php',
            absolutePath: 'tests/Fixtures/Traits/MyTrait.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Traits/MyTrait.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorIfANoneClassIsDetectedButIsExcluded(): void
    {
        $assertion = new AreClasses();

        $assertion->assert(new PendingFile(
            fileName: 'MyTrait.php',
            localPath: 'tests/Fixtures/Traits/MyTrait.php',
            absolutePath: 'tests/Fixtures/Traits/MyTrait.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Traits/MyTrait.php'))
        ), false, [MyTrait::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassIsAClass(): void
    {
        $assertion = new AreClasses();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClassIsAClassButShouldntBe(): void
    {
        $assertion = new AreClasses();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/MyClass.php is a class");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAErroringClassIsExcluded(): void
    {
        $assertion = new AreClasses();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), negate: true, except: [MyClass::class]);

        $this->assertTrue(true);
    }
}
