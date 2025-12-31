<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\GarminAccount;
use App\Models\GarminActivity;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GarminService
{
    /**
     * Importar actividades de un alumno específico.
     */
    public function importActivities(Alumno $alumno)
    {
        $account = $alumno->garminAccount;
        if (!$account) {
            return;
        }

        // Verificar si el token ha expirado y refrescarlo si es necesario
        if ($account->expires_at && $account->expires_at->isPast()) {
            $this->refreshToken($account);
        }

        try {
            // En una implementación real, aquí llamaríamos a la API de Garmin
            // Garmin Health API: GET /wellness-api/rest/activities

            // Simulación de respuesta de Garmin
            $activities = $this->simulateGarminActivities($alumno->id);

            foreach ($activities as $data) {
                GarminActivity::updateOrCreate(
                    [
                        'alumno_id' => $alumno->id,
                        'garmin_activity_id' => $data['garmin_activity_id']
                    ],
                    [
                        'id' => $data['id'] ?? 'gact' . bin2hex(random_bytes(10)),
                        'name' => $data['name'],
                        'activity_type' => $data['activity_type'],
                        'start_time' => $data['start_time'],
                        'distance' => $data['distance'],
                        'duration' => $data['duration'],
                        'average_speed' => $data['average_speed'],
                        'max_speed' => $data['max_speed'],
                        'calories' => $data['calories'],
                        'average_hr' => $data['average_hr'],
                        'max_hr' => $data['max_hr'],
                        'raw_data' => $data['raw_data'],
                    ]
                );
            }

            Log::info("Importadas " . count($activities) . " actividades de Garmin para el alumno {$alumno->id}");

        } catch (\Exception $e) {
            Log::error("Error al importar actividades de Garmin: " . $e->getMessage());
        }
    }

    /**
     * Refrescar el token de acceso.
     */
    private function refreshToken(GarminAccount $account)
    {
        // Lógica para refrescar el token usando el refresh_token
        // Garmin OAuth 2.0 refresh flow
    }

    /**
     * Simular actividades de Garmin para propósitos de demostración.
     */
    private function simulateGarminActivities($alumnoId)
    {
        return [
            [
                'garmin_activity_id' => '8765432101',
                'name' => 'Carrera matutina',
                'activity_type' => 'RUNNING',
                'start_time' => now()->subDays(1)->setTime(8, 30),
                'distance' => 5000.5,
                'duration' => 1500,
                'average_speed' => 3.33,
                'max_speed' => 4.5,
                'calories' => 350,
                'average_hr' => 145,
                'max_hr' => 170,
                'raw_data' => ['source' => 'simulation', 'device' => 'Forerunner 245']
            ],
            [
                'garmin_activity_id' => '8765432102',
                'name' => 'Ciclismo tarde',
                'activity_type' => 'CYCLING',
                'start_time' => now()->subDays(2)->setTime(18, 00),
                'distance' => 25000.0,
                'duration' => 3600,
                'average_speed' => 6.94,
                'max_speed' => 10.2,
                'calories' => 800,
                'average_hr' => 135,
                'max_hr' => 160,
                'raw_data' => ['source' => 'simulation', 'device' => 'Edge 530']
            ]
        ];
    }

    /**
     * Manejar el webhook de Garmin (Push notifications).
     */
    public function handleWebhook(array $payload)
    {
        // Garmin envía un array de actividades
        if (isset($payload['activities'])) {
            foreach ($payload['activities'] as $activityData) {
                $garminUserId = $activityData['userId'];
                $account = GarminAccount::where('garmin_user_id', $garminUserId)->first();

                if ($account) {
                    $this->importActivities($account->alumno);
                }
            }
        }
    }
}
