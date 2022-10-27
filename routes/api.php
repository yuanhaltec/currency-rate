<?php

use App\Http\Controllers\CurrencyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('currencies')->group(function() {
    Route::get('/', [CurrencyController::class, 'index']);
    Route::post('/', [CurrencyController::class, 'create']);
    Route::put('/{id}', [CurrencyController::class, 'update']);
    Route::delete('/{id}', [CurrencyController::class, 'delete']);
    Route::get('/{from}/{to}', [CurrencyController::class, 'rate']);
});