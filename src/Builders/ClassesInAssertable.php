<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use Jpeters8889\PhpUnitCodeAssertions\Concerns\GetsPathFromNamespace;

class ClassesInAssertable extends CodeAssertable
{
    use GetsPathFromNamespace;

    public function __construct(string $pathOrNamespace)
    {
        if(str_contains($pathOrNamespace, '\\')) {
            $pathOrNamespace = $this->getPathFromNamespace($pathOrNamespace);
        }

        parent::__construct($pathOrNamespace);
    }
}
