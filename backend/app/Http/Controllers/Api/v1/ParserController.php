<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRequest;
use App\Services\Parser\ParserService;
use App\Services\Parser\ParserUploadService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParserController extends Controller
{
    public function __construct(
        private ParserUploadService $parserUploadService
    ) {
    }

    public function index(): Response
    {
        $list = [];

        return response($list);
    }

    public function upload(UploadRequest $request): Response
    {
        $uploadedFilePath = $this->parserUploadService->uploadFile($request);

        $resultParsedData = (new ParserService($uploadedFilePath))->process();

        $result = [
            'process' => 'upload',
            'data' => $resultParsedData
        ];

        return response($result);
    }
}
