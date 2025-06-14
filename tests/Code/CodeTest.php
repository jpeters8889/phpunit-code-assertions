<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Code;

use Jpeters8889\PhpUnitCodeAssertions\Builders\AssertableBuilder;
use Jpeters8889\PhpUnitCodeAssertions\CodeAssertionsTestCase;
use Jpeters8889\PhpUnitCodeAssertions\Contracts\Assertable;
use PHPUnit\Framework\Attributes\Test;

class CodeTest extends CodeAssertionsTestCase
{
    #[Test]
    public function allFilesDeclareStrictTypes(): void
    {
        $this->assertCodeIn('src')->usesStrictTypes();
    }

    #[Test]
    public function allAssertionClassesImplementTheAssertableInterface(): void
    {
        $this->assertClassesIn('src/Assertions')->implement(Assertable::class);
    }

    #[Test]
    public function allClassesInTheBuilderDirectory(): void
    {
        $this->assertClassesIn('src/Builders')
            ->extends(AssertableBuilder::class)->except(AssertableBuilder::class)
            ->hasSuffix('AssertableBuilder');
    }

    #[Test]
    public function allClassesInConcernsDirectoryAreTraits(): void
    {
        $this->assertClassesIn('src/Concerns')->areTraits();
    }

    #[Test]
    public function allClassesInContractsDirectoryAreInterfaces(): void
    {
        $this->assertClassesIn('src/Contracts')->areInterfaces();
    }

    #[Test]
    public function allClassesInDtoDirectoryAreReadOnly(): void
    {
        $this->assertClassesIn('src/Dto')->areReadOnly();
    }
}
