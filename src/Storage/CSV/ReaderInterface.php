<?php

namespace Storage\CSV;

interface ReaderInterface
{
    public function index(): int;

    public function current(): string|array;

//    public function header(): array|bool;

    public function nextRow(): bool;

    public function isValid(): bool;

    public function rewind(): void;

    public function all(): array;
}
