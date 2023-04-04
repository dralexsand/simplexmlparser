<?php

namespace App\Services\Parser;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;

class UploadService
{
    public function uploadFile(Request $request): string
    {
        $file = json_decode($request->getContent());

        $fileContent = base64_decode($file->file);
        $fileExtension = $file->fileExtension;
        $fileMimeType = $file->fileMimeType;
        $fileName = $file->fileName;

        $fileNameWithoutExtension = str_replace(".{$fileExtension}", "", $fileName);

        $mark = date('Y_m_d_H_i_s');
        $uploadedFileName = "{$fileNameWithoutExtension}_{$mark}.{$fileExtension}";
        $uploadedFilePath = "parser/{$uploadedFileName}";

        Storage::put($uploadedFilePath, $fileContent);

        return $uploadedFilePath;
    }
}
