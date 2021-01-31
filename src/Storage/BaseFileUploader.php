<?php

namespace Storage;

class BaseFileUploader implements UploaderInterface
{
    /**
     * Return true on success
     *
     * @param $path
     * @return bool
     */
    public function save(string $path): bool
    {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
            return false;
        }

        return true;
    }

    final public function getFileName(): string
    {
        return $_FILES['file']['name'];
    }

    final public function getFileSize(): int
    {
        return $_FILES['file']['size'];
    }
}
