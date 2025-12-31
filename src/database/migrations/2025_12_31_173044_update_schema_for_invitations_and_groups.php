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
        if (!Schema::hasColumn('grupos', 'profesorId')) {
            Schema::table('grupos', function (Blueprint $table) {
                $table->string('profesorId')->nullable()->after('id');
                $table->foreign('profesorId')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('settings', 'userId')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('userId')->nullable()->after('id');
                $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
                // Quitar el unique de key porque ahora puede haber la misma key para diferentes usuarios
                $table->dropUnique(['key']);
                $table->unique(['userId', 'key']);
            });
        }

        if (!Schema::hasTable('invitaciones')) {
            Schema::create('invitaciones', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->string('profesorId');
                $table->string('grupoId')->nullable();
                $table->string('token')->unique();
                $table->string('status')->default('pending'); // pending, accepted, expired
                $table->timestamp('accepted_at')->nullable();
                $table->timestamps();

                $table->foreign('profesorId')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('grupoId')->references('id')->on('grupos')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invitaciones');

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['userId', 'key']);
            $table->unique('key');
            $table->dropForeign(['userId']);
            $table->dropColumn('userId');
        });

        Schema::table('grupos', function (Blueprint $table) {
            $table->dropForeign(['profesorId']);
            $table->dropColumn('profesorId');
        });
    }
};
