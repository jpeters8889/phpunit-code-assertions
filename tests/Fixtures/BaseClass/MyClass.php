<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\BaseClass;

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
