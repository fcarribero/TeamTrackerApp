<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competencias', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('alumno_id');
            $table->string('nombre');
            $table->datetime('fecha');
            $table->text('observaciones')->nullable();
            $table->text('plan_carrera')->nullable();
            $table->string('tiempo_objetivo')->nullable();
            $table->text('resultado_obtenido')->nullable();
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competencias');
    }
};
