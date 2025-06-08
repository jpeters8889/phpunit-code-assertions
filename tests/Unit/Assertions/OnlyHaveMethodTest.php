<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Composer\Autoload\ClassLoader;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasMethods;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\OnlyHaveMethod;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\InvokableClass\MyClass;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OnlyHaveMethodTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassHasMoreThanOneMethod(): void
    {
        $assertion = new OnlyHaveMethod('__invoke');

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('tests/Fixtures/MyClass.php does not have exactly one method');

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenAClassHasMoreThanOneMethodButIsExcluded(): void
    {
        $assertion = new OnlyHaveMethod('__invoke');

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, [\Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itCanDetectWhenAClassDoesntHaveMethods(): void
    {
        $assertion = new OnlyHaveMethod('__invoke');

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('tests/Fixtures/ClassUsesTrait/ClassWithTrait.php does not have method __invoke');

        $assertion->assert(new PendingFile(
            fileName: 'ClassWithTrait.php',
            localPath: 'tests/Fixtures/ClassUsesTrait/ClassWithTrait.php',
            absolutePath: 'tests/Fixtures/ClassUsesTrait/ClassWithTrait.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/ClassUsesTrait/ClassWithTrait.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorIfAClassOnlyHasTheGivenMethod(): void
    {
        $assertion = new OnlyHaveMethod('__invoke');

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/InvokableClass/MyClass.php',
            absolutePath: 'tests/Fixtures/InvokableClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/InvokableClass/MyClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsIfAClassOnlyHasTheOneMethodWhenItShouldnt(): void
    {
        $assertion = new OnlyHaveMethod('__invoke');

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('tests/Fixtures/InvokableClass/MyClass.php only contains method __invoke');

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/InvokableClass/MyClass.php',
            absolutePath: 'tests/Fixtures/InvokableClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/InvokableClass/MyClass.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAClassIsExcluded(): void
    {
        $assertion = new OnlyHaveMethod('__invoke');

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/InvokableClass/MyClass.php',
            absolutePath: 'tests/Fixtures/InvokableClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/InvokableClass/MyClass.php'))
        ), negate: true, except: [MyClass::class]);

        $this->assertTrue(true);
    }
}
