<?php
namespace App\Interfaces;

interface FileServiceInterface {

    public static function uploadFile($request);

    public static function getUploadedFile($request);
}


