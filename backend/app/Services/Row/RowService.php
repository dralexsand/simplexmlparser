<?php

namespace App\Services\Row;

use App\Models\Row;

class RowService
{
    /**
     * @return array
     */
    public function getRowList(): array
    {
        $rows = Row::orderBy('date')->get();

        $list = [];

        foreach ($rows as $row) {
            $list[$row->date][] = $row;
        }

        return $list;
    }
}
