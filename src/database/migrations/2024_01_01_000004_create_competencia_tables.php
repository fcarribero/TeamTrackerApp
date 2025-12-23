<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competencias_preestablecidas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombre');
            $table->string('tipo');
            $table->string('distancia')->nullable();
            $table->timestamp('fecha')->nullable();
            $table->text('linkCompetencia')->nullable();
            $table->text('linkClasificaciones')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('objetivos_competencias', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('alumnoId');
            $table->string('competenciaPreestablecidaId')->nullable();
            $table->string('nombre')->nullable();
            $table->string('tipo');
            $table->string('distancia')->nullable();
            $table->timestamp('fecha');
            $table->string('tiempoObjetivo')->nullable();
            $table->string('numeroDorsal')->nullable();
            $table->string('resultado')->nullable();
            $table->text('notasProfesor')->nullable();
            $table->timestamps();
            $table->foreign('alumnoId')->references('id')->on('alumnos')->onDelete('cascade');
            $table->foreign('competenciaPreestablecidaId', 'oc_cp_id_foreign')->references('id')->on('competencias_preestablecidas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objetivos_competencias');
        Schema::dropIfExists('competencias_preestablecidas');
    }
};
