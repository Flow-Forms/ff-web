<?php

use App\Http\Controllers\Api\DocumentationSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/docs/search', [DocumentationSearchController::class, 'search']);
