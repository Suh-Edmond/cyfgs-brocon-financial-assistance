<?php
namespace App\Interfaces;

interface FileServiceInterface {

    public function uploadFile($request);

    public function getUploadedFile($request);
}


