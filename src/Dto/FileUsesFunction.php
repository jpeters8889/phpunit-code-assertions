<?php

declare(strict_types=1);

namespace Jpeters8889\PhpUnitCodeAssertions\Dto;

readonly class FileUsesFunction
{
    public function __construct(
        public string $filePath,
        public string $functionName,
    ) {
        //
    }
}
