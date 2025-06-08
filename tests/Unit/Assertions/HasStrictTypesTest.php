<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasStrictTypes;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\StrictClass\MyClass;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HasStrictTypesTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function ItErrorsWhenAClassIsntStrictTypes(): void
    {
        $assertion = new HasStrictTypes();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/BaseClass/MyClass does not declare strict types.");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorWhenAClassDoesntHaveStrictTypesWhenItIsExcluded(): void
    {
        $assertion = new HasStrictTypes();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, [\Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\BaseClass\MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfHasStrictTypes(): void
    {
        $assertion = new HasStrictTypes();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/StrictClass/MyClass.php',
            absolutePath: 'tests/Fixtures/StrictClass/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/StrictClass/MyClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsWhenAClassIsStrictlyTypedButShouldntBe(): void
    {
        $assertion = new HasStrictTypes();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/StrictClass/MyClass declares strict types.");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/StrictClass/MyClass',
            absolutePath: 'tests/Fixtures/StrictClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/StrictClass/MyClass.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorWhenAnExcludedFileFails(): void
    {
        $assertion = new HasStrictTypes();

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/StrictClass/MyClass',
            absolutePath: 'tests/Fixtures/StrictClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/StrictClass/MyClass.php'))
        ), negate: true, except: [MyClass::class]);

        $this->assertTrue(true);
    }
}
