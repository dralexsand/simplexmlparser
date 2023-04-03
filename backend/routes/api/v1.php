<?php

use App\Http\Controllers\Api\v1\ParserController;
use Illuminate\Support\Facades\Route;


/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::name('api.')->group(function () {
    Route::name('parser.')->prefix('parser')->group(function () {
        Route::get('/', [ParserController::class, 'index'])->name('index');
        Route::post('/', [ParserController::class, 'upload'])->middleware('auth.basic')->name('upload');
    });
});
