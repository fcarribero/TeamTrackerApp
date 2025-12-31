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
        Schema::create('profesor_alumno', function (Blueprint $table) {
            $table->id();
            $table->string('profesor_id');
            $table->string('alumno_id');
            $table->timestamps();

            $table->unique(['profesor_id', 'alumno_id']);
            $table->foreign('profesor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesor_alumno');
    }
};
