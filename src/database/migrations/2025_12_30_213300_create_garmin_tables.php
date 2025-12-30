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
        Schema::create('garmin_accounts', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('alumno_id');
            $table->string('garmin_user_id')->nullable();
            $table->text('access_token');
            $table->text('token_secret')->nullable(); // Para OAuth 1.0a si se usa
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
        });

        Schema::create('garmin_activities', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('alumno_id');
            $table->string('garmin_activity_id')->unique();
            $table->string('name')->nullable();
            $table->string('activity_type')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->double('distance')->default(0);
            $table->integer('duration')->default(0);
            $table->double('average_speed')->nullable();
            $table->double('max_speed')->nullable();
            $table->integer('calories')->nullable();
            $table->integer('average_hr')->nullable();
            $table->integer('max_hr')->nullable();
            $table->json('raw_data')->nullable(); // Guardar toda la información detallada en JSON
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garmin_activities');
        Schema::dropIfExists('garmin_accounts');
    }
};
