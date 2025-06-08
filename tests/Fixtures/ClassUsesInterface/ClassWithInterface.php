<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\ClassUsesInterface;

use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Contracts\MyInterface;

class ClassWithInterface implements MyInterface
{
    public function hello(): string
    {
        return ucwords(lcfirst('hello'));
    }
}
