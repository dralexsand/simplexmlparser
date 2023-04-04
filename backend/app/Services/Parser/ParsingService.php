<?php

namespace App\Services\Parser;

use App\Models\Row;
use App\Services\Progress\ProgressService;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParsingService
{
    private string $breakCondition;
    private int $lastRow;
    private bool $isProcess;
    private bool $parsingSuccess;
    private string $parsingMessage;

    public function __construct()
    {
        // set chars as mark of end parsing
        $this->setBreakCondition(env('PARSER_BREAK_CONDITION_PARSE', ''));
        // set last row as 0
        $this->setLastRow();
        // marked process as true on start parsing
        $this->setIsProcess(true);
        $this->setParsingSuccess(true);
    }

    public function getSpecificSheet(string $pathFile, int $sheetIndex = 0): Worksheet
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        $file = Storage::path($pathFile);

        $spreadsheet = $reader->load($file);

        return $spreadsheet->getSheet($sheetIndex);
    }

    public function process(string $pathFile, int $sheetId, string $sessionId)
    {
        $sheet = $this->getSpecificSheet($pathFile, $sheetId);

        $i = 0;

        $date = date('Y_m_d_h_i_s');

        while ($this->getIsProcess()) {
            $offset = $i * 1000;
            $limit = 1000;

            $data = $this->chunkParser($sheet, $offset, $limit);

            (new StorageService())->store($data);

            $count = $this->getIsProcess() ? $offset + $limit : $this->getLastRow();

            $prefix = "uuid_{$date}_{$i}_";

            (new ProgressService())->storeProgress($sessionId, $count, $prefix);

            $i++;
        }
    }

    public function chunkParser(Worksheet $sheet, int $offset, int $limit)
    {
        $parsedData = $this->parse($sheet, $offset, $limit);
        return $this->separationRowsUpdateCreate($parsedData);
    }

    public function parse(Worksheet $sheet, int $offset, int $limit): array
    {
        return $this->getItemsList($sheet, $offset, $limit);
    }

    public function separationRowsUpdateCreate(array $parsedData): array
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
     * @throws Exception
     */
    public function getItemsList(
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
     * @throws Exception
     */
    public function buildRowObject(Worksheet $sheet, int $row): array
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
    public function getProcessRow(int $rowId): string
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

    public function mapColumns(): array
    {
        return [
            'A' => 'row_id',
            'B' => 'name',
            'C' => 'date',
        ];
    }
}
