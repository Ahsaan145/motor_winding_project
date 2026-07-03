<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataSheetController;

Route::post('data-sheets', [DataSheetController::class, 'store']);
Route::get('data-sheets/draft', [DataSheetController::class, 'getDraft']);

Route::get('/users', [DataSheetController::class, 'index']);
Route::get('/users/{id}', [DataSheetController::class, 'show']);
Route::delete('/users/{id}', [DataSheetController::class, 'destroy']);

Route::get('/export-data-sheets-csv', [DataSheetController::class, 'exportCSV']);
Route::post('/import-data-sheets-csv', [DataSheetController::class, 'importCSV']);

Route::delete('/data-sheets-all', [DataSheetController::class, 'destroyAll']);