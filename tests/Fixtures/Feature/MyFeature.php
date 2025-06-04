<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Feature;

use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Contracts\MyInterface;
use Jpeters8889\PhpUnitCodeAssertions\Tests\Fixtures\Traits\MyTrait;

class MyFeature implements MyInterface
{
    use MyTrait;

    public function hello(): string
    {
        return ucwords(lcfirst('hello'));
    }
}
