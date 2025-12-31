<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $gruposAlumnos = DB::table('grupos_alumnos')
            ->join('grupos', 'grupos_alumnos.grupoId', '=', 'grupos.id')
            ->select('grupos.profesorId', 'grupos_alumnos.alumnoId')
            ->distinct()
            ->get();

        foreach ($gruposAlumnos as $relation) {
            if ($relation->profesorId && $relation->alumnoId) {
                DB::table('profesor_alumno')->updateOrInsert(
                    ['profesor_id' => $relation->profesorId, 'alumno_id' => $relation->alumnoId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
