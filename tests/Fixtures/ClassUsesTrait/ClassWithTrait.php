<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\ClassUsesTrait;

use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;

class ClassWithTrait
{
    use MyTrait;

    public function hello(): string
    {
        return ucwords(lcfirst('hello'));
    }
}
