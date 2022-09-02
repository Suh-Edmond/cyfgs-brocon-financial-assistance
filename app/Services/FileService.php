<?php

namespace App\Services;

use App\Interfaces\FileServiceInterface;
use App\Constants\FileStorageConstants;
use App\Http\Resources\FileUploadResource;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Http;

class FileService implements FileServiceInterface {

    use ResponseTrait;


    public static function uploadFile($request)
    {
        $organisation   = $request->user()->organisation;
        $directory      = ResponseTrait::getAppName()."/".$organisation->name."/".$request->directory;
        $url            = FileStorageConstants::BASE_URL."/upload-file?directory=".$directory."&fileCategory=".FileStorageConstants::FILE_STORAGE_BASE_DIRECTORY;

        Http::attach('file', file_get_contents($request->file('file')), $request->file('file')->getClientOriginalName())->post($url);
    }

    public static function getUploadedFile($request)
    {
        $organisation   = $request->user()->organisation;
        $directory      = ResponseTrait::getAppName()."/".$organisation->name."/".$request->directory;
        $file_name      = $request->file_name;

        $url            = FileStorageConstants::BASE_URL."/file?directory=".$directory."&fileCategory=".FileStorageConstants::FILE_STORAGE_BASE_DIRECTORY."&fileName=".$file_name;

        $file_response  = Http::get($url);

        $data           = json_decode($file_response);

        $response       = new FileUploadResource($data->fileName, $data->fileType, $data->fileSize, $data->fileDownloadUri);

        return $response;
    }
}
