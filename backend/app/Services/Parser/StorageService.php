<?php

namespace App\Services\Parser;


use App\Events\RowChangeEvent;
use App\Models\Row;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StorageService
{
    /**
     * @param array $separatedParsedData
     * @return array
     */
    public function store(array $separatedParsedData): void
    {
        if (key_exists('new', $separatedParsedData)) {
            $this->storeParsedData($separatedParsedData['new']);
        }

        if (key_exists('update', $separatedParsedData)) {
            $this->updateParsedData($separatedParsedData['update']);
        }
        //return $separatedParsedData;
    }

    /**
     * @param array $parsedData
     * @return void
     */
    public function storeParsedData(array $parsedData)
    {
        Row::insert($parsedData);
        // Event insert
        Storage::put('parser/logevents_insert.json', json_encode($parsedData));
        Log::info('LOG_EVENT:insert', $parsedData);

        $eventData = [
            'process' => 'insert',
            'data' => $parsedData,
        ];

        event(new RowChangeEvent($eventData));
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
        // Event update
        Storage::put('parser/logevents_update.json', json_encode($parsedData));
        Log::debug('LOG_EVENT:update', $parsedData);

        $eventData = [
            'process' => 'update',
            'data' => $parsedData,
        ];

        event(new RowChangeEvent($eventData));
    }
}
