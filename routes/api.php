<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketOperativoController;
use App\Http\Controllers\Api\TicketWorkflowController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update']);
    Route::patch('/tickets/{ticket}/operativo', [TicketOperativoController::class, 'update']);

    Route::post('/tickets/{ticket}/estado', [TicketWorkflowController::class, 'changeState']);
    Route::post('/tickets/{ticket}/cerrar', [TicketWorkflowController::class, 'close']);
    Route::post('/tickets/{ticket}/cancelar', [TicketWorkflowController::class, 'cancel']);
});
