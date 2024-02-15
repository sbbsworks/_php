<?php

declare(strict_types=1);

namespace App\Services\File;

use App\Exceptions\FileServiceException;
use App\Providers\Router\Routes;
use App\View\View;

class FileService
{
    private string $redirectPath = '';
    private string $filesUploadDirectory = 'uploads';
    private string $filesExtension = '.pdf';
    private string $defaultFileName = 'uploaded-file';
    private string $successText = 'Files uploaded: ';
    private string $errorText  = 'Could not upload';
    private string $unsupportedFileType = 'Unsupported file type: ';
    private string $maxFileSizeExceeded = 'Max file size exceeded';

    private string $uploadDirectoryStaticPath = '/data/app-data/uploads/';

    private array $allowedMimeTypes = [
        'application/pdf',
    ];
    private int $uploadedFileMaxSize = 2*10**6;
    private int $maxFileNumber = 1;
    public function __construct()
    {
        $this->redirectPath = Routes::Root->value . '/' .  Routes::File->value;
    }
    public function index(): View
    {
        return View::make(__METHOD__, ['fileIsUploaded' => $this->checkFileExists()]);
    }
    public function get(): void
    {
        $uploadsDirectoryPath = $this->getUploadsDirectoryPath();
        $filename = $this->getDefaultFileName();
        $fullPath = $uploadsDirectoryPath . $filename;
        $document = file_exists($fullPath) ? $fullPath : null;
        if(!$document) {
            header( "HTTP/1.1 404 Not Found" );
            exit;
        }
        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($document));
        header('Content-Disposition: inline; filename="' . $filename .'"');
        header('last-modified: ' . date ("F d Y H:i:s.", time()));
        header_remove(
            name: 'X-Powered-By'
        );
        readfile($document);
        exit;
    }
    public function create(): void
    {
        $allUploadedSanitizedDocuments = [];
        $errors = [];
        foreach($_FILES as $key => $_) {
            if (is_uploaded_file($_FILES[$key]['tmp_name'])) {
                $mime_type = mime_content_type($_FILES[$key]['tmp_name']);
                if(!in_array($mime_type, $this->allowedMimeTypes)) {
                    if($mime_type !== 'application/octet-stream') {
                        $errors[] = $this->unsupportedFileType . $mime_type;
                        continue;
                    }
                }
                $file_size = (int)$_FILES[$key]['size'];
                if($file_size > $this->uploadedFileMaxSize) {
                    $errors[] = $this->maxFileSizeExceeded . (int)$_FILES[$key]['size'] . ' (' . $this->uploadedFileMaxSize . ')';
                    continue;
                }
                
                $allUploadedSanitizedDocuments[] = $_FILES[$key]['tmp_name'];
            }
            if(count($allUploadedSanitizedDocuments) >= $this->maxFileNumber) {
                break;
            }
        }
        if(!count($allUploadedSanitizedDocuments) && count($_FILES)) {
            $errors[] = $this->maxFileSizeExceeded . ', allowed: ' . $this->uploadedFileMaxSize;
        }
        $uploadsDirectoryPath = $this->getUploadsDirectoryPath();
        if(!file_exists($uploadsDirectoryPath )) {
            throw new FileServiceException('Files upload directory doesn\'t not exist');
        }
        $uploaded = 0;
        foreach($allUploadedSanitizedDocuments as $file) {
            $filename = $this->getDefaultFileName();
            $uploaded += move_uploaded_file($file, $uploadsDirectoryPath  . $filename) ? 1 : 0;
        }
        if(!$uploaded) {
            $errors[] = $this->errorText;
        }
        
        session_start();
        $_SESSION['upload-results'] = $uploaded ? $this->successText . $uploaded : null;
        $_SESSION['upload-errors'] = count($errors) ? $errors : null;
        session_write_close();
        header("location: $this->redirectPath");
        exit;
    }

    public function checkFileExists(): bool
    {
        $uploadsDirectoryPath = $this->getUploadsDirectoryPath();
        $filename = $this->getDefaultFileName();
        $fullPath = $uploadsDirectoryPath . $filename;
        return file_exists($fullPath);
    }
    public function getDefaultFileName(): string
    {
        return '_' . $this->defaultFileName . $this->filesExtension;
    }
    public function getFullFilePath(string $file): string
    {
        return '/data/app-data/uploads/' . str_replace('_pdf', $this->filesExtension, $file);
    }

    public function unlink()
    {
        if(file_exists($this->uploadDirectoryStaticPath)) {
            foreach(glob($this->uploadDirectoryStaticPath . '/*') as $file) {
                if(str_contains('git', $file)) {
                    continue;
                }
                if(is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    private function getUploadsDirectoryPath(): string
    {
        return __DIR__ . "/../../../$this->filesUploadDirectory/";
    }
}
