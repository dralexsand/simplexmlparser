<?php

use App\Http\Controllers\Api\v1\ParserController;
use App\Http\Controllers\TestController;
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

Route::name('test.')->prefix('test')->group(function () {
    Route::get('/redis', [TestController::class, 'index']);
});
