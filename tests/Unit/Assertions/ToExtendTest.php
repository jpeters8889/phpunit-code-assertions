<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Composer\Autoload\ClassLoader;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToExtend;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\AbstractClass\AbstractClass;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\ClassExtend\ExtendedClass;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MyClass;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ToExtendTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itErrorsIfTheClassDoesntExtendTheGivenClass(): void
    {
        $assertion = new ToExtend(AbstractClass::class);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('tests/Fixtures/MyClass.php does not extend '.AbstractClass::class);

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorIfAClassDoesNotExtendTheGivenClassWhenItIsExcluded(): void
    {
        $assertion = new ToExtend(AbstractClass::class);

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, [MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassExtendsTheGivenClass(): void
    {
        $assertion = new ToExtend(AbstractClass::class);

        $assertion->assert(new PendingFile(
            fileName: 'ExtendedClass.php',
            localPath: 'tests/Fixtures/ClassExtend/ExtendedClass.php',
            absolutePath: 'tests/Fixtures/ClassExtend/ExtendedClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/ClassExtend/ExtendedClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClassExtendsAClassWhenItShouldnt(): void
    {
        $assertion = new ToExtend(AbstractClass::class);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('tests/Fixtures/ClassExtend/ExtendedClass.php extends '.AbstractClass::class);

        $assertion->assert(new PendingFile(
            fileName: 'ExtendedClass.php',
            localPath: 'tests/Fixtures/ClassExtend/ExtendedClass.php',
            absolutePath: 'tests/Fixtures/ClassExtend/ExtendedClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/ClassExtend/ExtendedClass.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAClassIsExcluded(): void
    {
        $assertion = new ToExtend(AbstractClass::class);

        $assertion->assert(new PendingFile(
            fileName: 'ExtendedClass.php',
            localPath: 'tests/Fixtures/ClassExtend/ExtendedClass.php',
            absolutePath: 'tests/Fixtures/ClassExtend/ExtendedClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/ClassExtend/ExtendedClass.php'))
        ), negate: true, except: [ExtendedClass::class]);

        $this->assertTrue(true);
    }
}
