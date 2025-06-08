<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Composer\Autoload\ClassLoader;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MyClass;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AreTraitsTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassIsntATrait(): void
    {
        $assertion = new AreTraits();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/MyClass.php is not a trait");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenAFileIsNotATraitWhenItIsExcluded(): void
    {
        $assertion = new AreTraits();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, [MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassIsATrait(): void
    {
        $assertion = new AreTraits();

        $assertion->assert(new PendingFile(
            fileName: 'MyTrait.php',
            localPath: 'tests/Fixtures/Traits/MyTrait.php',
            absolutePath: 'tests/Fixtures/Traits/MyTrait.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Traits/MyTrait.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClassIsATraitWhenItShouldntBe(): void
    {
        $assertion = new AreTraits();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/Traits/MyTrait.php is a trait");

        $assertion->assert(new PendingFile(
            fileName: 'MyTrait.php',
            localPath: 'tests/Fixtures/Traits/MyTrait.php',
            absolutePath: 'tests/Fixtures/Traits/MyTrait.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Traits/MyTrait.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAFileIsExcluded(): void
    {
        $assertion = new AreTraits();

        $assertion->assert(new PendingFile(
            fileName: 'MyTrait.php',
            localPath: 'tests/Fixtures/Traits/MyTrait.php',
            absolutePath: 'tests/Fixtures/Traits/MyTrait.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Traits/MyTrait.php'))
        ), negate: true, except: [MyTrait::class]);

        $this->assertTrue(true);
    }
}
