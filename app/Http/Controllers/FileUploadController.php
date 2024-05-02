<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\GetFileRequest;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    private FileService $file_service;

    public function __construct(FileService $file_service)
    {
        $this->file_service = $file_service;
    }


    public function uploadFile(FileUploadRequest $request)
    {
        $filePath = $this->file_service->uploadFile($request);

        return $this->sendResponse($filePath, 'File upload successfully');
    }

    public function getUploadFile(Request $request)
    {
        $filePath = $this->file_service->getUploadedFile($request);

        return $this->sendResponse($filePath, "success");
    }
}
