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
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('profesorId')->nullable()->after('id');
            $table->foreign('profesorId')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('competencias', function (Blueprint $table) {
            $table->string('profesorId')->nullable()->after('id');
            $table->foreign('profesorId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['profesorId']);
            $table->dropColumn('profesorId');
        });

        Schema::table('competencias', function (Blueprint $table) {
            $table->dropForeign(['profesorId']);
            $table->dropColumn('profesorId');
        });
    }
};
