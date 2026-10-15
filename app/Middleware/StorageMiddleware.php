<?php
namespace App\Middleware;

use App\Models\Submits;

class StorageMiddleware
{
    private int $userId;
    private int $courseId;

    public function __construct(?int $userId = 0, ?int $courseId = 0)
    {
        $this->userId = $userId;
        $this->courseId = $courseId;
    }

    private function moveFiles(string $fileTmp, string $directory, ?string $fileName)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
            // echo 'Directory exists. uploading'; // ? DEBUG
        }
        if (move_uploaded_file($fileTmp, $directory . $fileName)) {
            $filePath = $directory . $fileName;
            $relativeFilePath = str_replace(BASE_DIR . '/', '', $filePath);  // REMOVE ABSOLUTE PATH
            // * ADD TO DB
            $newSubmit = new Submits();
            $newSubmit->insertSubmits($this->userId, $this->courseId, $relativeFilePath);
        } else {
            // TODO :ERROR LOG
        }
    }

    public function storeUploads(array $file, string $courseTitle)
    {
        if ($file['error'] === UPLOAD_ERR_OK) {  // If Upload successfull with no errors
            $fileName = $file['name'];
            $fileType = $file['type'];
            $fileTmp = $file['tmp_name'];
            $fileSize = $file['size'];

            $allowedTypes = [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'image/jpeg',
                'image/png'
            ];

            if (!in_array($fileType, $allowedTypes)) {
                return 'wrong type';
            }

            $maxFileSize = 1 * 1024 * 1024;
            if ($fileSize > $maxFileSize) {
                return 'file too big';
            }

            $sanitizedFileName = preg_replace('/[^a-zA-Z0-9_.\-]/', '', basename($fileName));  // BASENAME STRIP ALL DIRECTORY PATH INFO - SECURITY MEASURE
            $uniqFileName = 'file_' . uniqid() . '_' . $sanitizedFileName;

            $directory = UPLOADS_DIR . $courseTitle . '/' . 'USER_ID_' . $this->userId . '/';
            $this->moveFiles($fileTmp, $directory, $uniqFileName);
        } else {
            return 'no file';
        }
    }

    public function storeLogs(string $data)
    {
        $logFile = fopen(LOGS_DIR . 'log.txt', 'a');
        fwrite($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $data . "\n");
        fclose($logFile);
    }
}
