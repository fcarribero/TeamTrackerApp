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
    protected $description = 'Sincroniza el clima de las ubicaciones de los usuarios';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\WeatherService $weatherService)
    {
        $this->info('Iniciando sincronización de clima...');

        $locations = \App\Models\User::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->select('latitud', 'longitud')
            ->distinct()
            ->get();

        foreach ($locations as $location) {
            $this->info("Sincronizando clima (últimos 30 días) para: {$location->latitud}, {$location->longitud}");
            $weatherService->syncHistory((float)$location->latitud, (float)$location->longitud, 30);
        }

        $this->info('Sincronización finalizada.');
    }
}
