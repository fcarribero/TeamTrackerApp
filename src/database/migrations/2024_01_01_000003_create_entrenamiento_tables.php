<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantillas_entrenamiento', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->text('contenido');
            $table->timestamps();
        });

        Schema::create('entrenamientos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->timestamp('fecha');
            $table->string('titulo');
            $table->string('alumnoId')->nullable();
            $table->string('plantillaId')->nullable();
            $table->string('plantillaNombre')->nullable();
            $table->text('ejercicios')->nullable();
            $table->text('contenidoPersonalizado')->nullable();
            $table->timestamps();
            $table->foreign('alumnoId')->references('id')->on('alumnos')->onDelete('cascade');
            $table->foreign('plantillaId')->references('id')->on('plantillas_entrenamiento')->onDelete('set null');
        });

        Schema::create('entrenamientos_grupos', function (Blueprint $table) {
            $table->string('entrenamientoId');
            $table->string('grupoId');
            $table->timestamp('createdAt')->useCurrent();
            $table->unique(['entrenamientoId', 'grupoId']);
            $table->foreign('entrenamientoId')->references('id')->on('entrenamientos')->onDelete('cascade');
            $table->foreign('grupoId')->references('id')->on('grupos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrenamientos_grupos');
        Schema::dropIfExists('entrenamientos');
        Schema::dropIfExists('plantillas_entrenamiento');
    }
};
