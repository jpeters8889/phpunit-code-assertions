<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Composer\Autoload\ClassLoader;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreInterfaces;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Contracts\MyInterface;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AreInterfacesTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassIsntAnInterface(): void
    {
        $assertion = new AreInterfaces();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/Traits/MyTrait.php is not an interface");

        $assertion->assert(new PendingFile(
            fileName: 'MyTrait.php',
            localPath: 'tests/Fixtures/Traits/MyTrait.php',
            absolutePath: 'tests/Fixtures/Traits/MyTrait.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Traits/MyTrait.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenANonInterfaceIsDetectedButExcluded(): void
    {
        $assertion = new AreInterfaces();

        $assertion->assert(new PendingFile(
            fileName: 'MyTrait.php',
            localPath: 'tests/Fixtures/Traits/MyTrait.php',
            absolutePath: 'tests/Fixtures/Traits/MyTrait.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Traits/MyTrait.php'))
        ), false, [MyTrait::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAnInterfaceIsAnInterface(): void
    {
        $assertion = new AreInterfaces();

        $assertion->assert(new PendingFile(
            fileName: 'MyInterface.php',
            localPath: 'tests/Fixtures/Contracts/MyInterface.php',
            absolutePath: 'tests/Fixtures/Contracts/MyInterface.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Contracts/MyInterface.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClasIsAnInterfaceWhenItShouldntBe(): void
    {
        $assertion = new AreInterfaces();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/Contracts/MyInterface.php is an interface");

        $assertion->assert(new PendingFile(
            fileName: 'MyInterface.php',
            localPath: 'tests/Fixtures/Contracts/MyInterface.php',
            absolutePath: 'tests/Fixtures/Contracts/MyInterface.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Contracts/MyInterface.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAFailingClassIsExcluded(): void
    {
        $assertion = new AreInterfaces();

        $assertion->assert(new PendingFile(
            fileName: 'MyInterface.php',
            localPath: 'tests/Fixtures/Contracts/MyInterface.php',
            absolutePath: 'tests/Fixtures/Contracts/MyInterface.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Contracts/MyInterface.php'))
        ), negate: true, except: [MyInterface::class]);

        $this->assertTrue(true);
    }
}
