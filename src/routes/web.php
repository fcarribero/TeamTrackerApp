<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AnuncioController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetenciaController;
use App\Http\Controllers\EntrenamientoController;
use App\Http\Controllers\GarminController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PlantillaController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de Autenticación con Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/profesor', [StatsController::class, 'profesorDashboard'])->name('dashboard.profesor');
    Route::get('/dashboard/alumno', function() {
        return redirect()->route('alumno.entrenamientos');
    })->name('dashboard.alumno');

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
    Route::post('/dashboard/profesor/entrenamientos/estimar', [EntrenamientoController::class, 'estimarMetricas'])->name('entrenamientos.estimar');
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
    Route::post('/dashboard/alumno/entrenamientos/{id}/completar', [EntrenamientoController::class, 'completarAlumno'])->name('alumno.entrenamientos.completar');
    Route::get('/dashboard/alumno/pagos', [PagoController::class, 'indexAlumno'])->name('alumno.pagos');
    Route::get('/dashboard/alumno/competencias', [CompetenciaController::class, 'indexAlumno'])->name('alumno.competencias');
    Route::post('/dashboard/alumno/competencias', [CompetenciaController::class, 'storeAlumno'])->name('alumno.competencias.store');
    Route::get('/dashboard/alumno/competencias/{competencia}/edit', [CompetenciaController::class, 'editAlumno'])->name('alumno.competencias.edit');
    Route::put('/dashboard/alumno/competencias/{competencia}', [CompetenciaController::class, 'updateAlumno'])->name('alumno.competencias.update');
    Route::delete('/dashboard/alumno/competencias/{competencia}/delete', [CompetenciaController::class, 'destroyAlumno'])->name('alumno.competencias.destroy');

    // Rutas para Garmin
    Route::get('/auth/garmin', [GarminController::class, 'redirectToGarmin'])->name('auth.garmin');
    Route::get('/auth/garmin/callback', [GarminController::class, 'handleGarminCallback']);
    Route::post('/auth/garmin/disconnect', [GarminController::class, 'disconnect'])->name('auth.garmin.disconnect');
    Route::get('/dashboard/alumno/configuracion', [AlumnoController::class, 'configuracion'])->name('alumno.configuracion');

    // Rutas para Anuncios
    Route::get('/dashboard/profesor/anuncio', [AnuncioController::class, 'index'])->name('anuncios.index');
    Route::post('/dashboard/profesor/anuncio', [AnuncioController::class, 'store'])->name('anuncios.store');
    Route::post('/dashboard/profesor/anuncio/{anuncio}/toggle', [AnuncioController::class, 'toggle'])->name('anuncios.toggle');

    // Rutas para Competencias (Profesor)
    Route::get('/dashboard/profesor/competencias', [CompetenciaController::class, 'indexProfesor'])->name('competencias.index');
    Route::get('/dashboard/profesor/competencias/{competencia}/edit', [CompetenciaController::class, 'editProfesor'])->name('competencias.edit');
    Route::put('/dashboard/profesor/competencias/{competencia}', [CompetenciaController::class, 'updateProfesor'])->name('competencias.update');
    Route::delete('/dashboard/profesor/competencias/{competencia}', [CompetenciaController::class, 'destroy'])->name('competencias.destroy');

    // Rutas para Configuración
    Route::get('/dashboard/profesor/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/dashboard/profesor/settings', [SettingController::class, 'store'])->name('settings.store');

    // Rutas para Perfil/Ubicación
    Route::post('/dashboard/profile/location', [\App\Http\Controllers\ProfileController::class, 'updateLocation'])->name('profile.update-location');
});
