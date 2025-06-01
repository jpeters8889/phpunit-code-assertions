<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures;

class MyClass
{
    public function hello(): string
    {
        return ucwords(lcfirst('hello'));
    }
}
