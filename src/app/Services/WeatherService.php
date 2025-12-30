<?php

namespace App\Services;

use App\Models\WeatherHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    public function getWeather(float $lat, float $lon, Carbon $dateTime)
    {
        // Primero intentamos buscar en la base de datos local
        $hour = $dateTime->copy()->startOfHour();
        $stored = WeatherHistory::where('latitud', $lat)
            ->where('longitud', $lon)
            ->where('fecha_hora', $hour)
            ->first();

        if ($stored) {
            return $stored;
        }

        $isFuture = $dateTime->isFuture() && !$dateTime->isToday();
        $referenceDate = $isFuture ? $dateTime->copy()->subYear() : $dateTime;

        // Si no está, lo pedimos a la API
        if (!$dateTime->isToday()) {
            $weather = $this->fetchFromArchiveApi($lat, $lon, $referenceDate);
        } else {
            $weather = $this->fetchFromForecastApi($lat, $lon, $dateTime);
        }

        if ($weather && $isFuture) {
            // Si es futuro, nos aseguramos de marcarlo como histórico y quitar el cielo si es necesario
            // Como fetchFromArchiveApi devuelve un WeatherHistory o un object, lo manejamos
            if (is_object($weather)) {
                $weather->is_historical = true;
                $weather->cielo = null; // Requerimiento: no incluir estado del cielo en el futuro
            }
        }

        return $weather;
    }

    public function getDailyForecast(float $lat, float $lon, Carbon $date)
    {
        $dateStr = $date->format('Y-m-d');
        $cacheKey = "daily_weather_{$lat}_{$lon}_{$dateStr}";

        return cache()->remember($cacheKey, now()->addHours(12), function() use ($lat, $lon, $date) {
            $isFuture = $date->isFuture() && !$date->isToday();
            $referenceDate = $isFuture ? $date->copy()->subYear() : $date;

            // Para fechas futuras usamos el archivo del año pasado según requerimiento.
            // Para hoy usamos forecast, para el pasado usamos archive.
            $apiUrl = ($isFuture || !$date->isToday()) ? "https://archive-api.open-meteo.com/v1/archive" : "https://api.open-meteo.com/v1/forecast";

            try {
                $params = [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'hourly' => 'weather_code',
                    'daily' => 'temperature_2m_max,temperature_2m_min',
                    'timezone' => 'auto',
                    'start_date' => $referenceDate->format('Y-m-d'),
                    'end_date' => $referenceDate->format('Y-m-d'),
                ];

                $response = Http::get($apiUrl, $params);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!isset($data['hourly']) || !isset($data['daily'])) return null;

                    $morningCode = $data['hourly']['weather_code'][9] ?? null;
                    $afternoonCode = $data['hourly']['weather_code'][15] ?? null;
                    $nightCode = $data['hourly']['weather_code'][21] ?? null;

                    return (object) [
                        'min' => $data['daily']['temperature_2m_min'][0],
                        'max' => $data['daily']['temperature_2m_max'][0],
                        'mañana' => (!$isFuture && $morningCode !== null) ? $this->mapWeatherCode($morningCode) : null,
                        'tarde' => (!$isFuture && $afternoonCode !== null) ? $this->mapWeatherCode($afternoonCode) : null,
                        'noche' => (!$isFuture && $nightCode !== null) ? $this->mapWeatherCode($nightCode) : null,
                        'is_historical' => $isFuture,
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Error fetching daily forecast: " . $e->getMessage());
            }

            return null;
        });
    }

    public function syncHistory(float $lat, float $lon, int $days = 30)
    {
        $startDate = now()->subDays($days)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        try {
            $response = Http::get("https://archive-api.open-meteo.com/v1/archive", [
                'latitude' => $lat,
                'longitude' => $lon,
                'hourly' => 'temperature_2m,relative_humidity_2m,weather_code',
                'timezone' => 'auto',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            if ($response->successful()) {
                $this->processMultipleApiResponse($response->json(), $lat, $lon);
                return true;
            }
        } catch (\Exception $e) {
            Log::error("Error syncing history weather: " . $e->getMessage());
        }

        return false;
    }

    private function processMultipleApiResponse(array $data, float $lat, float $lon)
    {
        if (!isset($data['hourly'])) return;

        foreach ($data['hourly']['time'] as $index => $time) {
            $dateTime = Carbon::parse($time);

            // Solo guardamos si es pasado o actual
            if ($dateTime->isFuture()) continue;

            WeatherHistory::updateOrCreate(
                [
                    'latitud' => $lat,
                    'longitud' => $lon,
                    'fecha_hora' => $dateTime
                ],
                [
                    'temperatura' => $data['hourly']['temperature_2m'][$index],
                    'humedad' => $data['hourly']['relative_humidity_2m'][$index],
                    'cielo' => $this->mapWeatherCode($data['hourly']['weather_code'][$index]),
                    'descripcion' => $this->mapWeatherCodeDescription($data['hourly']['weather_code'][$index]),
                ]
            );
        }
    }

    private function fetchFromForecastApi(float $lat, float $lon, Carbon $dateTime)
    {
        try {
            $response = Http::get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $lat,
                'longitude' => $lon,
                'hourly' => 'temperature_2m,relative_humidity_2m,weather_code',
                'timezone' => 'auto',
                'start_date' => $dateTime->format('Y-m-d'),
                'end_date' => $dateTime->format('Y-m-d'),
            ]);

            if ($response->successful()) {
                return $this->processApiResponse($response->json(), $lat, $lon, $dateTime);
            }
        } catch (\Exception $e) {
            Log::error("Error fetching forecast weather: " . $e->getMessage());
        }

        return null;
    }

    private function fetchFromArchiveApi(float $lat, float $lon, Carbon $dateTime)
    {
        try {
            $response = Http::get("https://archive-api.open-meteo.com/v1/archive", [
                'latitude' => $lat,
                'longitude' => $lon,
                'hourly' => 'temperature_2m,relative_humidity_2m,weather_code',
                'timezone' => 'auto',
                'start_date' => $dateTime->format('Y-m-d'),
                'end_date' => $dateTime->format('Y-m-d'),
            ]);

            if ($response->successful()) {
                return $this->processApiResponse($response->json(), $lat, $lon, $dateTime);
            }
        } catch (\Exception $e) {
            Log::error("Error fetching archive weather: " . $e->getMessage());
        }

        return null;
    }

    private function processApiResponse(array $data, float $lat, float $lon, Carbon $dateTime)
    {
        if (!isset($data['hourly'])) return null;

        $targetHour = $dateTime->copy()->startOfHour()->format('Y-m-d\TH:00');
        $index = array_search($targetHour, $data['hourly']['time']);

        if ($index === false) {
            // Si no encontramos la hora exacta, buscamos la más cercana
            $times = $data['hourly']['time'];
            $index = 0;
            $minDiff = PHP_INT_MAX;
            $targetTimestamp = $dateTime->copy()->startOfHour()->timestamp;

            foreach ($times as $i => $time) {
                $diff = abs(Carbon::parse($time)->timestamp - $targetTimestamp);
                if ($diff < $minDiff) {
                    $minDiff = $diff;
                    $index = $i;
                }
            }
        }

        $weatherData = [
            'latitud' => $lat,
            'longitud' => $lon,
            'fecha_hora' => Carbon::parse($data['hourly']['time'][$index]),
            'temperatura' => $data['hourly']['temperature_2m'][$index],
            'humedad' => $data['hourly']['relative_humidity_2m'][$index],
            'cielo' => $this->mapWeatherCode($data['hourly']['weather_code'][$index]),
            'descripcion' => $this->mapWeatherCodeDescription($data['hourly']['weather_code'][$index]),
            'icono' => $this->mapWeatherIcon($data['hourly']['weather_code'][$index]),
        ];

        // Guardar en histórico si es pasado o actual
        if ($weatherData['fecha_hora']->isPast() || $weatherData['fecha_hora']->isToday()) {
             return WeatherHistory::updateOrCreate(
                [
                    'latitud' => $lat,
                    'longitud' => $lon,
                    'fecha_hora' => $weatherData['fecha_hora']
                ],
                $weatherData
            );
        }

        return (object) $weatherData;
    }

    private function mapWeatherCode(int $code): string
    {
        if ($code === 0) return 'Despejado';
        if (in_array($code, [1, 2, 3])) return 'Nublado';
        if (in_array($code, [45, 48])) return 'Niebla';
        if (in_array($code, [51, 53, 55, 56, 57])) return 'Llovizna';
        if (in_array($code, [61, 63, 65, 66, 67])) return 'Lluvia';
        if (in_array($code, [71, 73, 75, 77])) return 'Nieve';
        if (in_array($code, [80, 81, 82])) return 'Chubascos';
        if (in_array($code, [95, 96, 99])) return 'Tormenta';

        return 'Desconocido';
    }

    private function mapWeatherCodeDescription(int $code): string
    {
        $descriptions = [
            0 => 'Cielo despejado',
            1 => 'Principalmente despejado', 2 => 'Parcialmente nublado', 3 => 'Nublado',
            45 => 'Niebla', 48 => 'Niebla con escarcha',
            51 => 'Llovizna ligera', 53 => 'Llovizna moderada', 55 => 'Llovizna densa',
            61 => 'Lluvia ligera', 63 => 'Lluvia moderada', 65 => 'Lluvia fuerte',
            80 => 'Chubascos ligeros', 81 => 'Chubascos moderados', 82 => 'Chubascos violentos',
            95 => 'Tormenta eléctrica',
        ];

        return $descriptions[$code] ?? 'Desconocido';
    }

    private function mapWeatherIcon(int $code): string
    {
        if ($code === 0) return 'sun';
        if (in_array($code, [1, 2, 3])) return 'cloud-sun';
        if (in_array($code, [45, 48])) return 'smog';
        if (in_array($code, [51, 53, 55, 56, 57])) return 'cloud-rain';
        if (in_array($code, [61, 63, 65, 66, 67])) return 'cloud-showers-heavy';
        if (in_array($code, [71, 73, 75, 77])) return 'snowflake';
        if (in_array($code, [80, 81, 82])) return 'cloud-sun-rain';
        if (in_array($code, [95, 96, 99])) return 'bolt';

        return 'sun';
    }
}
