<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    public function estimateTrainingMetrics(array $contenido)
    {
        if (empty($this->apiKey)) {
            Log::warning('ChatGPTService: OPENAI_API_KEY no configurada.');
            return null;
        }

        $calentamiento = $contenido['calentamiento'] ?? 'No especificado';
        $trabajoPrincipal = $contenido['trabajo_principal'] ?? 'No especificado';
        $enfriamiento = $contenido['enfriamiento'] ?? 'No especificado';

        $prompt = "Calculá paso a paso el tiempo total y la distancia estimada para el siguiente entrenamiento.
        - Antecedentes: El corredor tiene un CCL de 6:00 min/km promedio. Su ritmo de 10k es de 3:50 min/km.
        - Calentamiento: {$calentamiento}
        - Trabajo Principal: {$trabajoPrincipal}
        - Enfriamiento: {$enfriamiento}
        ";

        try {
            $payload = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Sos un entrenador experto en running. Todos los cálculos deben ser precisos, paso a paso y verificados. Verificá los números antes de responder. Razona mas tiempo. Calcula la distancia total en kilómetros y el tiempo total en minutos. Si para algún bloque no se especifica distancia o ritmo o dice CCL o Carrera Continua Lenta, asume un ritmo de 6:00 min/km. Calculá paso a paso el tiempo total. Devuelve exclusivamente un objeto JSON válido con los atributos "distancia" y "tiempo", sin markdown ni explicaciones.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.1,
                'max_completion_tokens' => 1000,
            ];

            $response = Http::withToken($this->apiKey)
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $content = json_decode($data['choices'][0]['message']['content'], true);

                Log::channel('openai')->info('OpenAI Request/Response', [
                    'model' => $this->model,
                    'request' => $payload,
                    'response' => $data,
                    'usage' => $data['usage'] ?? 'N/A'
                ]);

                return [
                    'distancia' => $content['distancia'] ?? 0,
                    'tiempo' => $content['tiempo'] ?? 0,
                ];
            }

            $errorBody = $response->json();
            Log::channel('openai')->error('OpenAI Error Response', [
                'model' => $this->model,
                'request' => $payload,
                'response' => $errorBody,
            ]);

            Log::error('ChatGPTService Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::channel('openai')->critical('OpenAI Exception', [
                'model' => $this->model,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            Log::error('ChatGPTService Exception: ' . $e->getMessage());
            return null;
        }
    }
}
