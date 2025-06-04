<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Composer\Autoload\ClassLoader;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreClasses;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\AreTraits;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\ToImplement;
use Jpeters8889\PhpUnitCodeAssertions\Assertions\UsesFunctions;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsAbsolutePath;
use Jpeters8889\PhpUnitCodeAssertions\Concerns\RetrievesFiles;
use Jpeters8889\PhpUnitCodeAssertions\Dto\PendingFile;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Contracts\MyInterface;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ToImplementTest extends TestCase
{
    use GetsAbsolutePath;

    #[Test]
    public function itCanDetectWhenAClassDoesNotImplementTheInterface(): void
    {
        $assertion = new ToImplement(MyInterface::class);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('tests/Fixtures/MockAssertable.php does not implement '.MyInterface::class);

        $assertion->assert(new PendingFile(
            fileName: 'MockAssertable.php',
            localPath: 'tests/Fixtures/MockAssertable.php',
            absolutePath: 'tests/Fixtures/MockAssertable.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MockAssertable.php'))
        ));
    }

    #[Test]
    public function itDoesntErrorIfAClassUsesTheInterface(): void
    {
        $assertion = new ToImplement(MyInterface::class);

        $assertion->assert(new PendingFile(
            fileName: 'MyClass.php',
            localPath: 'tests/Fixtures/MyClass.php',
            absolutePath: 'tests/Fixtures/MyClass.php',
            contents: file_get_contents($this->getAbsolutePath('tests/Fixtures/MyClass.php'))
        ));

        $this->assertTrue(true);
    }
}
