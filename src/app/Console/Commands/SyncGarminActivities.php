<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncGarminActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'garmin:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las actividades de Garmin para todos los alumnos conectados';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\GarminService $garminService)
    {
        $this->info('Iniciando sincronización de Garmin...');

        $accounts = \App\Models\GarminAccount::with('alumno')->get();

        foreach ($accounts as $account) {
            $this->info("Sincronizando alumno: {$account->alumno->nombre}");
            $garminService->importActivities($account->alumno);
        }

        $this->info('Sincronización finalizada.');
    }
}
