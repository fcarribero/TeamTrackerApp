<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrenamientos', function (Blueprint $table) {
            $table->text('observaciones')->nullable()->after('contenidoPersonalizado');
        });

        Schema::table('plantillas_entrenamiento', function (Blueprint $table) {
            $table->text('observaciones')->nullable()->after('contenido');
        });
    }

    public function down(): void
    {
        Schema::table('entrenamientos', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });

        Schema::table('plantillas_entrenamiento', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
    }
};
