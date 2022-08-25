<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\GetFileRequest;
use App\Services\FileService;

class FileUploadController extends Controller
{
    private $file_service;

    public function __construct(FileService $file_service)
    {
        $this->file_service = $file_service;
    }


    public function uploadFile(FileUploadRequest $request)
    {

        $this->file_service->uploadFile($request);

        return $this->sendResponse('success', 'File upload sucessfully');
    }

    public function getUploadFile(GetFileRequest $request)
    {
        $response = $this->file_service->getUploadedFile($request);

        return $this->sendResponse($response, 200);
    }
}
