<?php

namespace App\Services\Parser;

use App\Models\Row;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParserService
{
    private string $filePath;
    private string $breakCondition;
    private int $lastRow;
    private bool $isProcess;
    private bool $parsingSuccess;
    private string $parsingMessage;

    public function __construct(string $uploadedFilePath)
    {
        $this->setFilePath($uploadedFilePath);
        // set chars as mark of end parsing
        $this->setBreakCondition(env('PARSER_BREAK_CONDITION_PARSE', ''));
        // set last row as 0
        $this->setLastRow();
        // marked process as true on start parsing
        $this->setIsProcess(true);
        $this->setParsingSuccess(true);
        $this->setParsingMessage("file {$this->getFilePath()} parsed successful, db refresh");
    }

    public function process(int $sheetId = 0): array
    {
        $resultParsing = [];

        $i = 0;
        while ($this->getIsProcess()) {
            $offset = $i * 1000;
            $limit = 1000;
            $data = $this->chunkParser($offset, $limit, $sheetId);

            if (!$this->isParsingSuccess()) {
                return [
                    "success" => $this->isParsingSuccess(),
                    "message" => $this->getParsingMessage(),
                ];
            }

            $data = (new ParserStorageService())->store($data);

            $resultParsing[] = $data;

            $i++;
        }

        Storage::delete($this->getFilePath());

        return [
            "success" => $this->isParsingSuccess(),
            "message" => $this->getParsingMessage(),
            "data" => $resultParsing
        ];
    }

    private function chunkParser(int $offset, int $limit, int $sheetId)
    {
        $parsedData = $this->parse($offset, $limit, $sheetId);
        $separatedParsedData = $this->separationRowsUpdateCreate($parsedData);

        return $separatedParsedData;
    }

    public function parse(int $offset, int $limit, int $sheetId): array
    {
        $sheet = $this->getSpecificSheet($sheetId);

        return $this->getItemsList($sheet, $offset, $limit);
    }

    private function separationRowsUpdateCreate(array $parsedData): array
    {
        $processData = [];

        foreach ($parsedData as $data) {
            $markProcess = $this->getProcessRow($data['row_id']);
            $processData[$markProcess][] = $data;
        }

        return $processData;
    }

    /**
     * @param Worksheet $sheet
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    protected function getItemsList(
        Worksheet $sheet,
        int $offset = 0,
        int $limit = 1000
    ): array {
        $data = [];

        $limitIndex = $offset + $limit;

        $row = $offset === 0 ? 1 : $offset;

        while ($row < $limitIndex && $this->getIsProcess()) {
            if (!$this->isCompleteParsing($row)) {
                $buildRow = $this->buildRowObject($sheet, $row);

                // if mark 'end' - return last row
                if ($buildRow['status'] === 'end') {
                    // set last row
                    $this->setLastRow((int)$row);
                    // marked process as false
                    $this->setIsProcess(false);
                    break;
                } elseif ($buildRow['status'] === 'process') {
                    $data[] = $buildRow['data'];
                }
                $row++;
            }
        }

        return $data;
    }


    /**
     * @param int $sheetIndex
     * @return Worksheet
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function getSpecificSheet(int $sheetIndex = 0): Worksheet
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        $file = Storage::path($this->getFilePath());

        $spreadsheet = $reader->load($file);

        return $spreadsheet->getSheet(0);
    }

    /**
     * @param int $row
     * @return bool
     */
    protected function isCompleteParsing(int $row): bool
    {
        if ($this->getLastRow() === 0) {
            return false;
        }

        return $row > $this->getLastRow();
    }

    /**
     * @param Worksheet $sheet
     * @param int $row
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    protected function buildRowObject(Worksheet $sheet, int $row): array
    {
        $data = [];
        $status = 'process';

        $firstCell = "A$row";
        $firstValue = $sheet->getCell($firstCell)->getCalculatedValue();

        if (trim($firstValue) === $this->getBreakCondition()) {
            $data = $row;
            $status = "end";
        } elseif (!is_numeric($firstValue)) {
            $status = 'skip';
        } else {
            $mapColumns = $this->mapColumns();

            foreach ($mapColumns as $column => $field) {
                $cell = "$column$row";

                // rules
                $data[$field] = match ($column) {
                    'A' => (int)$sheet->getCell($cell)->getCalculatedValue(),
                    'B' => $sheet->getCell($cell)->getValue(),
                    'C' => $this->convertFormatDate($sheet->getCell($cell)->getValue()),
                };
            }
        }

        return [
            'data' => $data,
            'status' => $status,
        ];
    }

    /**
     * @param int $rowId
     * @return string
     */
    protected function getProcessRow(int $rowId): string
    {
        $rowDb = Row::where('row_id', $rowId)->get()->toArray();

        return !empty($rowDb) ? 'update' : 'new';
    }

    public function convertFormatDate($excelFormattedDate)
    {
        return date("Y-m-d", ((int)$excelFormattedDate - 25569) * 86400);
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
     * @return string
     */
    public function getBreakCondition(): string
    {
        return $this->breakCondition;
    }

    /**
     * @param string $breakCondition
     */
    public function setBreakCondition(string $breakCondition): void
    {
        $this->breakCondition = $breakCondition;
    }

    /**
     * @return int
     */
    public function getLastRow(): int
    {
        return $this->lastRow;
    }

    /**
     * @param int $lastRow
     */
    public function setLastRow(int $lastRow = 0): void
    {
        $this->lastRow = $lastRow;
    }

    /**
     * @return bool
     */
    public function getIsProcess(): bool
    {
        return $this->isProcess;
    }

    /**
     * @param bool $isProcess
     */
    public function setIsProcess(bool $isProcess): void
    {
        $this->isProcess = $isProcess;
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

    public function mapColumns(): array
    {
        return [
            'A' => 'row_id',
            'B' => 'name',
            'C' => 'date',
        ];
    }
}
