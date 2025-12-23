<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EntrenamientoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PlantillaController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/profesor', [StatsController::class, 'profesorDashboard'])->name('dashboard.profesor');
    Route::get('/dashboard/alumno', [StatsController::class, 'alumnoDashboard'])->name('dashboard.alumno');

    // Rutas para Alumnos (Profesor)
    Route::resource('/dashboard/profesor/alumnos', AlumnoController::class)->names([
        'index' => 'alumnos.index',
        'create' => 'alumnos.create',
        'store' => 'alumnos.store',
        'show' => 'alumnos.show',
        'edit' => 'alumnos.edit',
        'update' => 'alumnos.update',
        'destroy' => 'alumnos.destroy',
    ]);

    // Rutas para Grupos
    Route::resource('/dashboard/profesor/grupos', GrupoController::class)->names([
        'index' => 'grupos.index',
        'create' => 'grupos.create',
        'store' => 'grupos.store',
        'edit' => 'grupos.edit',
        'update' => 'grupos.update',
        'destroy' => 'grupos.destroy',
    ]);

    // Rutas para Pagos
    Route::resource('/dashboard/profesor/pagos', PagoController::class)->names([
        'index' => 'pagos.index',
        'create' => 'pagos.create',
        'store' => 'pagos.store',
        'edit' => 'pagos.edit',
        'update' => 'pagos.update',
        'destroy' => 'pagos.destroy',
    ]);

    // Rutas para Entrenamientos y Plantillas
    Route::resource('/dashboard/profesor/entrenamientos', EntrenamientoController::class)->names([
        'index' => 'entrenamientos.index',
        'create' => 'entrenamientos.create',
        'store' => 'entrenamientos.store',
        'edit' => 'entrenamientos.edit',
        'update' => 'entrenamientos.update',
        'destroy' => 'entrenamientos.destroy',
    ]);
    Route::resource('/dashboard/profesor/plantillas', PlantillaController::class)->names([
        'index' => 'plantillas.index',
        'create' => 'plantillas.create',
        'store' => 'plantillas.store',
        'edit' => 'plantillas.edit',
        'update' => 'plantillas.update',
        'destroy' => 'plantillas.destroy',
    ]);

    // Rutas para Alumno
    Route::get('/dashboard/alumno/entrenamientos', [EntrenamientoController::class, 'indexAlumno'])->name('alumno.entrenamientos');
});
