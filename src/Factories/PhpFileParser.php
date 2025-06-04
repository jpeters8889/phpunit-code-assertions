<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Factories;

use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;

class PhpFileParser
{
    /**
     * @return null|Stmt[]
     */
    public static function parse(string $fileContents): null|array
    {
        return (new ParserFactory())
            ->createForNewestSupportedVersion()
            ->parse($fileContents);
    }
}
