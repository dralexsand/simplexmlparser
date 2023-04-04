<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Row;
use App\Services\Row\RowService;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function __construct(private RowService $rowService)
    {
    }

    public function index()
    {
        $data = $this->rowService->getRowList();

        return response($data);
    }
}
