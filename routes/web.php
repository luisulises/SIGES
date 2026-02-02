<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\MetricasController;
use App\Http\Controllers\TicketController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return redirect()->route('tickets.index');
})->middleware(['auth', 'ensure.active'])->name('dashboard');

Route::middleware(['auth', 'ensure.active'])->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    Route::get('/admin/usuarios', [AdminController::class, 'users'])
        ->middleware('role:admin')
        ->name('admin.users');
    Route::get('/admin/catalogos', [AdminController::class, 'catalogs'])
        ->middleware('role:admin')
        ->name('admin.catalogs');

    Route::get('/busqueda', [BusquedaController::class, 'index'])
        ->middleware('role:admin,coordinador')
        ->name('search');
    Route::get('/metricas', [MetricasController::class, 'index'])
        ->middleware('role:admin,coordinador')
        ->name('metrics');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
