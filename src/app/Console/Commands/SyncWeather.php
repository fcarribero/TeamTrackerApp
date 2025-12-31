<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza el clima de las ubicaciones de los usuarios y competencias';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\WeatherService $weatherService)
    {
        $this->info('Iniciando sincronización de clima...');

        $userLocations = \App\Models\User::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->select('latitud', 'longitud')
            ->get();

        $compLocations = \App\Models\Competencia::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->select('latitud', 'longitud')
            ->get();

        $locations = $userLocations->concat($compLocations)
            ->map(function($loc) {
                return [
                    'latitud' => (float) $loc->latitud,
                    'longitud' => (float) $loc->longitud
                ];
            })
            ->unique(function ($item) {
                return $item['latitud'] . '|' . $item['longitud'];
            });

        $this->info('Se encontraron ' . $locations->count() . ' ubicaciones únicas para sincronizar.');

        foreach ($locations as $location) {
            $this->info("Sincronizando clima para: {$location['latitud']}, {$location['longitud']}");
            $weatherService->syncHistory($location['latitud'], $location['longitud'], 365);
        }

        $this->info('Sincronización finalizada.');
    }
}
