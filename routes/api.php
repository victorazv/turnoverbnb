<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->group(function (){
    Route::post('', [UserController::class, 'store']);

    Route::post('deposit', [UserController::class, 'deposit']);
    Route::post('buy', [UserController::class, 'buy']);
    Route::get('balance', [UserController::class, 'getBalance']);
});

Route::prefix('admin')->group(function (){
    Route::get('pending-checks', [UserController::class, 'getPendingChecks']);//
    Route::post('approve-deposit', [UserController::class, 'approveDeposit']);//
});


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
