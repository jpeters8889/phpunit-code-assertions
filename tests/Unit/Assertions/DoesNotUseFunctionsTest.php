<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Unit\Assertions;

use Jpeters8889\PhpUnitCodeAssertions\Assertions\DoesNotUseFunctions;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DoesNotUseFunctionsTest extends TestCase
{
    #[Test]
    public function itCanDetectWhenAFileContainsAForbiddenFunction(): void
    {
        $assertion = new DoesNotUseFunctions('./../../Fixtures', ['ucwords']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that path does not use functions\n./../../Fixtures/MyClass.php uses function ucwords()");

        $assertion->assert();
    }

    #[Test]
    public function itCanDetectWhenAFileContainsMultipleForbiddenFunction(): void
    {
        $assertion = new DoesNotUseFunctions('./../../Fixtures', ['ucwords', 'lcfirst']);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed asserting that path does not use functions\n./../../Fixtures/MyClass.php uses function ucwords()\n./../../Fixtures/MyClass.php uses function lcfirst()");

        $assertion->assert();
    }

    #[Test]
    public function itDoesntErrorIfAClassDoesNotUseAnForbiddenFunction(): void
    {
        $assertion = new DoesNotUseFunctions('./../../Fixtures', ['assert']);

        $assertion->assert();

        $this->assertTrue(true);
    }
}
