<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrenamientos_alumnos', function (Blueprint $table) {
            $table->string('entrenamientoId');
            $table->string('alumnoId');
            $table->timestamp('createdAt')->useCurrent();
            $table->unique(['entrenamientoId', 'alumnoId']);
            $table->foreign('entrenamientoId')->references('id')->on('entrenamientos')->onDelete('cascade');
            $table->foreign('alumnoId')->references('id')->on('alumnos')->onDelete('cascade');
        });

        // Migrar datos existentes de entrenamientos.alumnoId a la nueva tabla pivot
        $entrenamientos = DB::table('entrenamientos')->whereNotNull('alumnoId')->get();
        foreach ($entrenamientos as $entrenamiento) {
            DB::table('entrenamientos_alumnos')->insert([
                'entrenamientoId' => $entrenamiento->id,
                'alumnoId' => $entrenamiento->alumnoId,
            ]);
        }

        Schema::table('entrenamientos', function (Blueprint $table) {
            $table->dropForeign(['alumnoId']);
            $table->dropColumn('alumnoId');
        });
    }

    public function down(): void
    {
        Schema::table('entrenamientos', function (Blueprint $table) {
            $table->string('alumnoId')->nullable();
            $table->foreign('alumnoId')->references('id')->on('alumnos')->onDelete('cascade');
        });

        // Opcionalmente restaurar el primer alumno asignado (si lo hay)
        $pivots = DB::table('entrenamientos_alumnos')->get();
        foreach ($pivots as $pivot) {
            DB::table('entrenamientos')->where('id', $pivot->entrenamientoId)->update([
                'alumnoId' => $pivot->alumnoId
            ]);
        }

        Schema::dropIfExists('entrenamientos_alumnos');
    }
};
