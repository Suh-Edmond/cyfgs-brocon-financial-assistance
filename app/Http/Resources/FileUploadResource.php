<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileUploadResource extends JsonResource
{
    private  $file_name;
    private  $file_type;
    private  $file_size;
    private  $file_download_uri;

    public function __construct($file_name, $file_type, $file_size, $file_download_uri)
    {
        $this->file_name = $file_name;
        $this->file_type = $file_type;
        $this->file_size = $file_size;
        $this->file_download_uri = $file_download_uri;
    }

    public function toArray($request)
    {
        return [
            'file_name'            => $this->file_name,
            'file_type'            => $this->file_type,
            'file_size'            => $this->file_size,
            'file_download_uri'    => $this->file_download_uri
        ];
    }
}
