<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Competencia;
use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SyncWeatherTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_weather_command_processes_user_and_competition_locations()
    {
        // 1. Preparar datos: Usuario con ubicación
        User::create([
            'id' => 'user-1',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'latitud' => -34.6037,
            'longitud' => -58.3816, // Buenos Aires
        ]);

        Alumno::create([
            'id' => 'alumno-1',
            'nombre' => 'Test',
            'apellido' => 'Alumno',
            'userId' => 'user-1',
            'fechaNacimiento' => '2000-01-01',
            'sexo' => 'masculino',
        ]);

        // 2. Preparar datos: Competencia con ubicación diferente
        Competencia::create([
            'id' => 'comp-1',
            'alumno_id' => 'alumno-1',
            'nombre' => 'Maratón de Londres',
            'fecha' => now()->addDays(10),
            'latitud' => 51.5074,
            'longitud' => -0.1278, // Londres
        ]);

        // Mockear WeatherService
        $weatherServiceMock = Mockery::mock(WeatherService::class);

        // Esperamos que se llame a syncHistory para las dos ubicaciones
        // Buenos Aires
        $weatherServiceMock->shouldReceive('syncHistory')
            ->with(-34.6037, -58.3816, 365)
            ->once()
            ->andReturn(true);

        // Londres
        $weatherServiceMock->shouldReceive('syncHistory')
            ->with(51.5074, -0.1278, 365)
            ->once()
            ->andReturn(true);

        $this->app->instance(WeatherService::class, $weatherServiceMock);

        // Ejecutar el comando
        $this->artisan('weather:sync')
            ->expectsOutput('Iniciando sincronización de clima...')
            ->expectsOutput('Se encontraron 2 ubicaciones únicas para sincronizar.')
            ->expectsOutput('Sincronizando clima para: -34.6037, -58.3816')
            ->expectsOutput('Sincronizando clima para: 51.5074, -0.1278')
            ->expectsOutput('Sincronización finalizada.')
            ->assertExitCode(0);
    }

    public function test_sync_weather_command_avoids_duplicate_locations()
    {
        // Usuario y Competencia en la misma ubicación
        User::create([
            'id' => 'user-1',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'latitud' => -34.6037,
            'longitud' => -58.3816,
        ]);

        Alumno::create([
            'id' => 'alumno-1',
            'nombre' => 'Test',
            'apellido' => 'Alumno',
            'userId' => 'user-1',
            'fechaNacimiento' => '2000-01-01',
            'sexo' => 'masculino',
        ]);

        Competencia::create([
            'id' => 'comp-1',
            'alumno_id' => 'alumno-1',
            'nombre' => 'Carrera Local',
            'fecha' => now()->addDays(5),
            'latitud' => -34.6037,
            'longitud' => -58.3816,
        ]);

        $weatherServiceMock = Mockery::mock(WeatherService::class);

        // Esperamos que se llame solo UNA VEZ
        $weatherServiceMock->shouldReceive('syncHistory')
            ->with(-34.6037, -58.3816, 365)
            ->once()
            ->andReturn(true);

        $this->app->instance(WeatherService::class, $weatherServiceMock);

        $this->artisan('weather:sync')
            ->expectsOutput('Se encontraron 1 ubicaciones únicas para sincronizar.')
            ->assertExitCode(0);
    }
}
