<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\StrictClass;

class MyClass
{
    public function hello(): string
    {
        return ucwords(lcfirst('hello'));
    }

    public function bye()
    {

    }
}
