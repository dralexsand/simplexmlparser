<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRequest;
use App\Models\Session;
use App\Services\Parser\ProcessService;
use App\Services\Parser\UploadService;
use App\Services\Session\SessionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParserController extends Controller
{
    public function __construct(
        private UploadService $parserUploadService,
        private SessionService $sessionService
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

        $sessionId = $this->sessionService->initSession();

        $resultParsedData = (new ProcessService($uploadedFilePath, $sessionId))->process();

        (new Session())
            ->where('session_id', $sessionId)
            ->update([
                'end_session' => date('Y-m-d H:i:s')
            ]);

        $result = [
            'process' => 'upload',
            'data' => $resultParsedData
        ];

        return response($result);
    }
}
