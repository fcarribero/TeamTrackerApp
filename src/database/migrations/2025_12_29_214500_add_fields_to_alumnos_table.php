<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('dni')->nullable()->after('id');
            $table->string('apellido')->nullable()->after('nombre');
            $table->string('obra_social')->nullable();
            $table->string('numero_socio')->nullable();
            $table->string('certificado_medico')->nullable();
            $table->date('vencimiento_certificado')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn([
                'dni',
                'apellido',
                'obra_social',
                'numero_socio',
                'certificado_medico',
                'vencimiento_certificado'
            ]);
        });
    }
};
