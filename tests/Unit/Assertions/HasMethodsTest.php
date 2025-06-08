<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Composer\Autoload\ClassLoader;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasMethods;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MyClass;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HasMethodsTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassDoesntHaveMethods(): void
    {
        $assertion = new HasMethods(['foo', 'bar']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that a class has methods,\ntests/Fixtures/MyClass.php does not have method foo()\ntests/Fixtures/MyClass.php does not have method bar()");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenAFileDoesntHaveAMethodWhenItIsExcluded(): void
    {
        $assertion = new HasMethods(['foo', 'bar']);

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, [MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassHasTheGivenMethods(): void
    {
        $assertion = new HasMethods(['hello', 'bye']);

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClassHasMethodsWhenItShouldnt(): void
    {
        $assertion = new HasMethods(['hello', 'bye']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("ailed asserting that a class does not have methods,\ntests/Fixtures/MyClass.php has method hello()\ntests/Fixtures/MyClass.php has method bye()");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAFailIsExcluded(): void
    {
        $assertion = new HasMethods(['hello', 'bye']);

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), negate: true, except: [MyClass::class]);

        $this->assertTrue(true);
    }
}
