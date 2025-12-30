<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weather_history', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->timestamp('fecha_hora');
            $table->float('temperatura')->nullable();
            $table->float('humedad')->nullable();
            $table->string('cielo')->nullable(); // Ej: Despejado, Nublado, Lluvia
            $table->string('descripcion')->nullable();
            $table->string('icono')->nullable();
            $table->timestamps();

            $table->unique(['latitud', 'longitud', 'fecha_hora'], 'weather_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_history');
    }
};
