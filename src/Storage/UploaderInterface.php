<?php

namespace Storage;

interface UploaderInterface
{
    public function save(string $path): mixed;

    public function getFileName(): string;

    public function getFileSize(): int;
}
