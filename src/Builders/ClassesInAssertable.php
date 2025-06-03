<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Builders;

use PhpParser\Node\Expr\Error;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Assert;
use Symfony\Component\Finder\SplFileInfo;

class ClassesInAssertable extends CodeAssertable
{
    public function isFileTestable(SplFileInfo $file): bool
    {
        try {
            $ast = (new ParserFactory())
                ->createForNewestSupportedVersion()
                ->parse($file->getContents());

            $classes = (new NodeFinder())->findInstanceOf($ast, Class_::class);

            if(count($classes) === 0) {
                return false;
            }
        } catch (Error) {
            Assert::fail("Unable to parse file: {$file->getPathname()}");
        }

        return true;
    }
}
