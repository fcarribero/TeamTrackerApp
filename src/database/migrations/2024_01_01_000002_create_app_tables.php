<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombre');
            $table->timestamp('fechaNacimiento');
            $table->string('sexo');
            $table->text('notas')->nullable();
            $table->string('userId')->nullable()->unique();
            $table->timestamps();
            $table->foreign('userId')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('grupos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('grupos_alumnos', function (Blueprint $table) {
            $table->string('grupoId');
            $table->string('alumnoId');
            $table->timestamp('createdAt')->useCurrent();
            $table->unique(['grupoId', 'alumnoId']);
            $table->foreign('grupoId')->references('id')->on('grupos')->onDelete('cascade');
            $table->foreign('alumnoId')->references('id')->on('alumnos')->onDelete('cascade');
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('alumnoId');
            $table->double('monto');
            $table->timestamp('fechaPago');
            $table->string('mesCorrespondiente');
            $table->string('estado');
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->foreign('alumnoId')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('grupos_alumnos');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('alumnos');
    }
};
