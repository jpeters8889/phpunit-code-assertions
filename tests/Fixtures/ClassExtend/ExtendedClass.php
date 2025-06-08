<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\ClassExtend;

use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\AbstractClass\AbstractClass;

class ExtendedClass extends AbstractClass
{
    public function hello(): string
    {
        return ucwords(lcfirst('hello'));
    }
}
