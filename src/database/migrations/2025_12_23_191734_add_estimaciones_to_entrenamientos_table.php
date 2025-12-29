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
        Schema::table('entrenamientos', function (Blueprint $table) {
            $table->double('distanciaTotal')->nullable()->after('contenidoPersonalizado');
            $table->integer('tiempoTotal')->nullable()->after('distanciaTotal'); // tiempo en minutos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrenamientos', function (Blueprint $table) {
            $table->dropColumn(['distanciaTotal', 'tiempoTotal']);
        });
    }
};
