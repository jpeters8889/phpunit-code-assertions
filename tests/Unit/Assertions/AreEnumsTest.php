<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreEnums;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Enums\MyEnum;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MyClass;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AreEnumsTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassIsntAnEnum(): void
    {
        $assertion = new AreEnums();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/MyClass.php is not an enum");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenAFileIsNotAnEnumWhenItIsExcluded(): void
    {
        $assertion = new AreEnums();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, [MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassIsAnEnum(): void
    {
        $assertion = new AreEnums();

        $assertion->assert(new PendingFile(
            fileName: 'MyTrait.php',
            localPath: 'tests/Fixtures/Enums/MyEnum.php',
            absolutePath: 'tests/Fixtures/Enums/MyEnum.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Enums/MyEnum.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClassIsAnEnumWhenItShouldntBe(): void
    {
        $assertion = new AreEnums();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/Enums/MyEnum.php is an enum");

        $assertion->assert(new PendingFile(
            fileName: 'MyEnum.php',
            localPath: 'tests/Fixtures/Enums/MyEnum.php',
            absolutePath: 'tests/Fixtures/Enums/MyEnum.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Enums/MyEnum.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAFileIsExcluded(): void
    {
        $assertion = new AreEnums();

        $assertion->assert(new PendingFile(
            fileName: 'MyEnum.php',
            localPath: 'tests/Fixtures/Enums/MyEnum.php',
            absolutePath: 'tests/Fixtures/Enums/MyEnum.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/Enums/MyEnum.php'))
        ), negate: true, except: [MyEnum::class]);

        $this->assertTrue(true);
    }
}
