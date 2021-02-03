<?php

namespace Storage\CSV;

interface ReaderInterface
{
    public function index(): int;

    public function current(): string|array;

    public function getHeader(): array|bool;

    public function nextRow(): bool;

    public function isValid(): bool;

    public function rewind(): void;
}
