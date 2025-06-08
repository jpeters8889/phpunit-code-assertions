<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures;

use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Contracts\MyInterface;

class MyClass implements MyInterface
{
    use MyTrait;

    public function hello(): string
    {
        return ucwords(lcfirst('hello'));
    }

    public function bye(): string
    {
        return ucwords(lcfirst('hello'));
    }
}
