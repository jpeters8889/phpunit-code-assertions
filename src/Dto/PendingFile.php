<?php

namespace Jpeters8889\PhpUnitCodeAssertions\Dto;

readonly class PendingFile
{
    public function __construct(
        public string $fileName,
        public string $localPath,
        public string $absolutePath,
        public string $contents,
        public ?string $fqns = null,
    )
    {
        //
    }
}
