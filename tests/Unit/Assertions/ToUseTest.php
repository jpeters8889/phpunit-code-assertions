<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MockAssertable;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\MyClass;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToUse;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ToUseTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassDoesNotImplementTheInterface(): void
    {
        $assertion = new ToUse(MyTrait::class);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('tests/Fixtures/MockAssertable.php does not use ' . MyTrait::class);

        $assertion->assert(new PendingFile(
            fileName: 'MockAssertable.php',
            localPath: 'tests/Fixtures/MockAssertable.php',
            absolutePath: 'tests/Fixtures/MockAssertable.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MockAssertable.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenAClassDoesntUseATraitWhenItIsExcluded(): void
    {
        $assertion = new ToUse(MyTrait::class);

        $assertion->assert(new PendingFile(
            fileName: 'MockAssertable.php',
            localPath: 'tests/Fixtures/MockAssertable.php',
            absolutePath: 'tests/Fixtures/MockAssertable.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MockAssertable.php'))
        ), false, [MockAssertable::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassUsesTheTrait(): void
    {
        $assertion = new ToUse(MyTrait::class);

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorWhenAClassUsesATraitWhenItShouldnt(): void
    {
        $assertion = new ToUse(MyTrait::class);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('tests/Fixtures/MyClass.php uses ' . MyTrait::class);

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAFileIsExcluded(): void
    {
        $assertion = new ToUse(MyTrait::class);

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ), negate: true, except: [MyClass::class]);

        $this->assertTrue(true);
    }
}
