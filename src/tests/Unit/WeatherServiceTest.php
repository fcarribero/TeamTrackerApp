<?php

namespace Tests\Unit;

use App\Models\WeatherHistory;
use App\Services\WeatherService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_history_uses_default_days_when_no_records_exist()
    {
        Http::fake([
            'archive-api.open-meteo.com/*' => Http::response(['hourly' => ['time' => [], 'temperature_2m' => []]]),
        ]);

        $service = new WeatherService();
        $lat = -34.6037;
        $lon = -58.3816;
        $days = 10;

        $service->syncHistory($lat, $lon, $days);

        Http::assertSent(function ($request) use ($days) {
            $expectedStartDate = now()->subDays($days)->format('Y-m-d');
            return $request->url() &&
                   str_contains($request->url(), 'archive-api.open-meteo.com') &&
                   $request['start_date'] === $expectedStartDate;
        });
    }

    public function test_sync_history_detects_last_record_and_optimizes_start_date()
    {
        $lat = -34.6037;
        $lon = -58.3816;
        $lastDate = Carbon::now()->subDays(5);

        // Crear un registro previo
        WeatherHistory::create([
            'latitud' => $lat,
            'longitud' => $lon,
            'fecha_hora' => $lastDate,
            'temperatura' => 20.0,
            'humedad' => 50.0,
            'cielo' => 'Despejado',
            'descripcion' => 'Cielo despejado'
        ]);

        Http::fake([
            'archive-api.open-meteo.com/*' => Http::response(['hourly' => ['time' => [], 'temperature_2m' => []]]),
        ]);

        $service = new WeatherService();
        $service->syncHistory($lat, $lon, 365); // Aunque pida 365 días

        Http::assertSent(function ($request) use ($lastDate) {
            $expectedStartDate = $lastDate->format('Y-m-d');
            return $request->url() &&
                   str_contains($request->url(), 'archive-api.open-meteo.com') &&
                   $request['start_date'] === $expectedStartDate;
        });
    }
}
