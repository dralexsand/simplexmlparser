<?php

namespace App\Services\Parser;


use App\Models\Row;

class ParserStorageService
{
    /**
     * @param array $separatedParsedData
     * @return array
     */
    public function store(array $separatedParsedData)
    {
        if (key_exists('new', $separatedParsedData)) {
            $this->storeParsedData($separatedParsedData['new']);
        }

        if (key_exists('update', $separatedParsedData)) {
            $this->updateParsedData($separatedParsedData['update']);
        }

        return $separatedParsedData;
    }

    /**
     * @param array $parsedData
     * @return void
     */
    public function storeParsedData(array $parsedData)
    {
        Row::insert($parsedData);
    }

    /**
     * @param array $parsedData
     * @return void
     */
    public function updateParsedData(array $parsedData)
    {
        foreach ($parsedData as $row) {
            (new Row())
                ->where('row_id', (int)$row['row_id'])
                ->update($row);
        }
    }
}
