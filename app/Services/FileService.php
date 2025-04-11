<?php

namespace App\Services;


use App\Exceptions\BusinessValidationException;
use App\Interfaces\FileServiceInterface;
use App\Constants\FileStorageConstants;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Storage;

class FileService implements FileServiceInterface {

    use ResponseTrait, HelpTrait;
    public function uploadFile($request)
    {
        $organisation   = $request->user()->organisation;
        $directory      = $organisation->id."/".$request->file_category;
        $fileName = $request->file('image')->getClientOriginalName();
        $fileName = str_replace(' ', '', $fileName);
         try {
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png'
            ]);

             $request->file('image')->storeAs(FileStorageConstants::FILE_STORAGE_BASE_DIRECTORY.$directory, $fileName, 'public');

             $filePath = FileStorageConstants::FILE_STORAGE_BASE_DIRECTORY.$directory."/".$fileName;

             $this->saveFile($filePath, $request);

        }catch (\Exception $exception){
            throw new BusinessValidationException($exception->getMessage(), 400);
        }

        return $filePath;
    }

    public function getUploadedFile($request)
    {
        return $this->getFilePath($request);
    }

    private function getFilePath($request)
    {
        $organisation   = $request->user()->organisation;

        $organisationName = str_replace(" ", "", $organisation->name);

        $directory      = $organisationName."/"."LOGOS"."/"."Picture1.png";

        return FileStorageConstants::FILE_STORAGE_BASE_DIRECTORY.$directory;
    }

    private function saveFile($filePath, $request)
    {
        if($request->requester == "ORGANISATION"){
            $request->user()->organisation()->update([
                'logo' => $filePath
            ]);
        }
        if($request->requester == "MEMBER"){
            $request->user()->update([
                'picture' => $filePath
            ]);
        }
    }

    private function removeStoredFile($request, $filePath){
        if ($request->requester == "ORGANISATION"){
            if(isset($request->user()->organisation->logo) && $request->user()->organisation->logo != $filePath){
                $path = explode("/storage/", $request->user()->organisation->logo)[1];
                Storage::delete($path);
            }
        }
        if($request->requester == "MEMBER") {
            if(isset($request->user()->picture) && $request->user()->picture != $filePath){
                $path = explode("/storage/", $request->user()->picture)[1];
                Storage::delete($path);
            }
        }
    }
}
