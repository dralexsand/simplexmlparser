<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParserController extends Controller
{
    public function index(): Response
    {
        $list = [];

        return response($list);
    }

    public function upload(Request $request): Response
    {
        $result = [];

        return response($result);
    }
}
