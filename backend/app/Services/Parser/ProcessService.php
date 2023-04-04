<?php

namespace App\Services\Parser;

use App\Jobs\ParsingJob;
use App\Models\Row;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProcessService
{
    private string $filePath;
    private bool $parsingSuccess;
    private string $parsingMessage;

    private string $sessionId;

    public function __construct(string $uploadedFilePath, string $sessionId)
    {
        $this->setFilePath($uploadedFilePath);
        $this->setSessionId($sessionId);
        $this->setParsingSuccess(true);
        $this->setParsingMessage("file {$this->getFilePath()} parsed successful, db refresh");
    }

    public function process(int $sheetId = 0): array
    {
        ParsingJob::dispatch(new ParsingService(), $this->getFilePath(), $sheetId, $this->getSessionId());

        //Storage::delete($this->getFilePath());

        return [
            "success" => $this->isParsingSuccess(),
            "message" => $this->getParsingMessage(),
        ];
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * @return bool
     */
    public function isParsingSuccess(): bool
    {
        return $this->parsingSuccess;
    }

    /**
     * @param bool $parsingSuccess
     */
    public function setParsingSuccess(bool $parsingSuccess): void
    {
        $this->parsingSuccess = $parsingSuccess;
    }

    /**
     * @return string
     */
    public function getParsingMessage(): string
    {
        return $this->parsingMessage;
    }

    /**
     * @param string $parsingMessage
     */
    public function setParsingMessage(string $parsingMessage): void
    {
        $this->parsingMessage = $parsingMessage;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

}
