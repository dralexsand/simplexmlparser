<?php

namespace App\Jobs;

use App\Services\Parser\ParsingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParsingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ParsingService $parsingService;
    public string $filePath;
    public int $sheetId;

    public string $sessionId;

    /**
     * @param ParsingService $parsingService
     * @param string $filePath
     * @param int $sheetId
     */
    public function __construct(ParsingService $parsingService, string $filePath, int $sheetId, string $sessionId)
    {
        $this->parsingService = $parsingService;
        $this->filePath = $filePath;
        $this->sheetId = $sheetId;
        $this->sessionId = $sessionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->parsingService->process($this->filePath, $this->sheetId, $this->sessionId);
    }
}
