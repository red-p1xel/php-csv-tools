<?php

namespace Storage;

use Http\Request;
use Http\Response;

final class UploadProcessor
{
    private array $allowedExtensions;

    private int $sizeLimit;

    /** @var false|BaseFileUploader */
    private $file;

    private string $nameModifier;

    private Request $request;

    /**
     * @param Request $request
     * @param array $allowedExtensions
     * @param int $sizeLimit
     */
    public function __construct(Request $request, array $allowedExtensions = ['csv'], int $sizeLimit = 5242880)
    {
        $this->request = $request;
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;
        $this->nameModifier = date('YmdHis');

        if (isset($_FILES['file']) && !empty($_FILES['file'])) {
            $this->file = new BaseFileUploader();
        } else {
            $this->file = false;
        }
    }

    /**
     * @param string $dir
     * @param bool $replaceFile
     * @return array|string|string[]
     */
    public function handle(string $dir, bool $replaceFile = false)
    {
        $uploadDirectory = $dir . date('Ymd') . '/';

        if (!file_exists($uploadDirectory)) {
            if (!mkdir($uploadDirectory)) {
                $error = ['error' => 'Could not create a directory.'];
                return Response::view($error);
            }
        }

        if (!is_dir($uploadDirectory)) {
            $error = ['error' => "Not a directory."];
            return Response::view($error);
        }

        if (!is_writable($uploadDirectory)) {
            $error = ['error' => "Permission denied."];
            return $error;
        }

        $size = $this->file->getFileSize();

        if ($size == 0) {
            $error = ['error' => 'Empty file.'];
            return Response::view($error);
        }

        if ($size > $this->sizeLimit) {
            $error = ['error' => 'File size too large.'];
            return Response::view($error);
        }

        $pathInfo = pathinfo($this->file->getFileName());
        $fileName = $pathInfo['filename'];
        $ext      = $pathInfo['extension'];
        $fileName = preg_replace('/^\.+/', '', $fileName);

        if ($fileName == '') {
            $error = ['error' => 'Invalid filename.'];
            return Response::view($error);
        }

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $error =['error' => 'Extension not allowed.'];
            return Response::view($error);
        }

        if (!$replaceFile) {
            $name = $fileName;
            while (file_exists($uploadDirectory . $fileName . '.' . $ext)) {
                $fileName = $name . sprintf("%14d", $this->nameModifier);
            }
        }

        if ($this->file->save($uploadDirectory . $fileName . '.' . $ext)) {
            return [
                'data' => [
                    'origin_name' => $this->file->getFileName(),
                    'storage_path' => $uploadDirectory . $fileName . '.' . $ext,
                ],
                'success' => true
            ];
        } else {
            $error = ['error' => 'Internal server error.'];
            return Response::view($error);
        }
    }
}
