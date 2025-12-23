<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrenamiento_resultados', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('entrenamientoId');
            $table->string('alumnoId');
            $table->text('sensacion')->nullable();
            $table->integer('dificultad')->default(1);
            $table->text('molestias')->nullable();
            $table->text('comentarios')->nullable();
            $table->timestamps();

            $table->foreign('entrenamientoId')->references('id')->on('entrenamientos')->onDelete('cascade');
            $table->foreign('alumnoId')->references('id')->on('alumnos')->onDelete('cascade');
            $table->unique(['entrenamientoId', 'alumnoId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrenamiento_resultados');
    }
};
