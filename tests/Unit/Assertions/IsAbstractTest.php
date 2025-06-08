<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Composer\Autoload\ClassLoader;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\IsAbstract;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\AbstractClass\AbstractClass;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\BaseClass\MyClass;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IsAbstractTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassIsntAbstract(): void
    {
        $assertion = new IsAbstract();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/BaseClass/MyClass is not an abstract class");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenAClassIsNotAbstractWhenItIsExcluded(): void
    {
        $assertion = new IsAbstract();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, [MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassIsAbstract(): void
    {
        $assertion = new IsAbstract();

        $assertion->assert(new PendingFile(
            fileName: 'AbstractClass.php',
            localPath: 'tests/Fixtures/AbstractClass/AbstractClass.php',
            absolutePath: 'tests/Fixtures/AbstractClass/AbstractClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/AbstractClass/AbstractClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClassIsAbstractWhenItShouldntBe(): void
    {
        $assertion = new IsAbstract();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/AbstractClass/AbstractClass.php is an abstract class");

        $assertion->assert(new PendingFile(
            fileName: 'AbstractClass.php',
            localPath: 'tests/Fixtures/AbstractClass/AbstractClass.php',
            absolutePath: 'tests/Fixtures/AbstractClass/AbstractClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/AbstractClass/AbstractClass.php'))
        ), true, []);
    }

    #[Test]
    public function itDoesntErrorWhenAFailingClassIsExcluded(): void
    {
        $assertion = new IsAbstract();

        $assertion->assert(new PendingFile(
            fileName: 'AbstractClass.php',
            localPath: 'tests/Fixtures/AbstractClass/AbstractClass.php',
            absolutePath: 'tests/Fixtures/AbstractClass/AbstractClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/AbstractClass/AbstractClass.php'))
        ), true, [AbstractClass::class]);

        $this->assertTrue(true);
    }
}
