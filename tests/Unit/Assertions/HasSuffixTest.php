<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\HasSuffix;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\BaseClass\MyClass;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HasSuffixTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itErrorsIfAClasDoesntEndInTheSuffix(): void
    {
        $assertion = new HasSuffix('Foo');

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/BaseClass/MyClass does not end with Foo");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, []);
    }

    #[Test]
    public function itDoesntErrorIfAClassDoesntHaveASuffixButItIsIgnored(): void
    {
        $assertion = new HasSuffix('Foo');

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, [MyClass::class]);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAFileDoesntHaveASuffixButItIsIgnored(): void
    {
        $assertion = new HasSuffix('Foo');

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), false, ['tests/Fixtures/BaseClass/MyClass.php']);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDoesntErrorIfAClassEndsInTheCorrectSuffix(): void
    {
        $assertion = new HasSuffix('class');

        $assertion->assert(new PendingFile(
            fileName: 'AbstractClass.php',
            localPath: 'tests/Fixtures/AbstractClass/AbstractClass.php',
            absolutePath: 'tests/Fixtures/AbstractClass/AbstractClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/AbstractClass/AbstractClass.php'))
        ), false, []);

        $this->assertTrue(true);
    }

    #[Test]
    public function itErrorsIfAClassEndsInASuffixWhenItShouldnt(): void
    {
        $assertion = new HasSuffix('Class');

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("tests/Fixtures/BaseClass/MyClass ends with Class");

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), negate: true, except: []);
    }

    #[Test]
    public function itDoesntErrorIfAnFailingClassIsExcluded(): void
    {
        $assertion = new HasSuffix('Class');

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/BaseClass/MyClass',
            absolutePath: 'tests/Fixtures/BaseClass/MyClass',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/BaseClass/MyClass.php'))
        ), negate: true, except: [MyClass::class]);

        $this->assertTrue(true);
    }
}
