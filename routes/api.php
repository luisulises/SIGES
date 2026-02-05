<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminRoleController;
use App\Http\Controllers\Api\AdminUsuarioController;
use App\Http\Controllers\Api\AdminSistemaCatalogoController;
use App\Http\Controllers\Api\AdminPrioridadCatalogoController;
use App\Http\Controllers\Api\AdminTipoSolicitudCatalogoController;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketAdjuntoController;
use App\Http\Controllers\Api\TicketBusquedaController;
use App\Http\Controllers\Api\TicketComentarioController;
use App\Http\Controllers\Api\TicketHistorialController;
use App\Http\Controllers\Api\TicketInvolucradoController;
use App\Http\Controllers\Api\TicketOperativoController;
use App\Http\Controllers\Api\TicketRelacionController;
use App\Http\Controllers\Api\TicketTiempoController;
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

Route::middleware(['auth:sanctum', 'ensure.active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);

    Route::get('/tickets/busqueda', [TicketBusquedaController::class, 'index'])->middleware('role:admin,coordinador');
    Route::get('/tickets/metricas', [TicketBusquedaController::class, 'metrics'])->middleware('role:admin,coordinador');

    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::patch('/tickets/{ticket}/operativo', [TicketOperativoController::class, 'update']);

    Route::get('/tickets/{ticket}/comentarios', [TicketComentarioController::class, 'index']);
    Route::post('/tickets/{ticket}/comentarios', [TicketComentarioController::class, 'store']);

    Route::get('/tickets/{ticket}/adjuntos', [TicketAdjuntoController::class, 'index']);
    Route::post('/tickets/{ticket}/adjuntos', [TicketAdjuntoController::class, 'store']);
    Route::get('/tickets/{ticket}/adjuntos/{adjunto}/download', [TicketAdjuntoController::class, 'download']);

    Route::get('/tickets/{ticket}/historial', [TicketHistorialController::class, 'index']);
    Route::get('/tickets/{ticket}/relaciones', [TicketRelacionController::class, 'index']);
    Route::post('/tickets/{ticket}/relaciones', [TicketRelacionController::class, 'store']);
    Route::get('/tickets/{ticket}/tiempo', [TicketTiempoController::class, 'index']);
    Route::post('/tickets/{ticket}/tiempo', [TicketTiempoController::class, 'store']);

    Route::get('/tickets/{ticket}/involucrados', [TicketInvolucradoController::class, 'index']);
    Route::post('/tickets/{ticket}/involucrados', [TicketInvolucradoController::class, 'store']);
    Route::delete('/tickets/{ticket}/involucrados/{usuario}', [TicketInvolucradoController::class, 'destroy']);

    Route::post('/tickets/{ticket}/estado', [TicketWorkflowController::class, 'changeState']);
    Route::post('/tickets/{ticket}/cerrar', [TicketWorkflowController::class, 'close']);
    Route::post('/tickets/{ticket}/cancelar', [TicketWorkflowController::class, 'cancel']);

    Route::get('/notificaciones', [NotificacionController::class, 'index']);
    Route::post('/notificaciones/{notificacion}/leer', [NotificacionController::class, 'markAsRead']);

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/roles', [AdminRoleController::class, 'index']);

        Route::get('/usuarios', [AdminUsuarioController::class, 'index']);
        Route::post('/usuarios', [AdminUsuarioController::class, 'store']);
        Route::patch('/usuarios/{usuario}', [AdminUsuarioController::class, 'update']);

        Route::get('/catalogos/sistemas', [AdminSistemaCatalogoController::class, 'index']);
        Route::post('/catalogos/sistemas', [AdminSistemaCatalogoController::class, 'store']);
        Route::patch('/catalogos/sistemas/{sistema}', [AdminSistemaCatalogoController::class, 'update']);

        Route::get('/catalogos/prioridades', [AdminPrioridadCatalogoController::class, 'index']);
        Route::post('/catalogos/prioridades', [AdminPrioridadCatalogoController::class, 'store']);
        Route::patch('/catalogos/prioridades/{prioridad}', [AdminPrioridadCatalogoController::class, 'update']);

        Route::get('/catalogos/tipos-solicitud', [AdminTipoSolicitudCatalogoController::class, 'index']);
        Route::post('/catalogos/tipos-solicitud', [AdminTipoSolicitudCatalogoController::class, 'store']);
        Route::patch('/catalogos/tipos-solicitud/{tipoSolicitud}', [AdminTipoSolicitudCatalogoController::class, 'update']);
    });
});
