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
        // 1. Asegurar que nombre y apellido tengan datos basados en 'name' antes de borrarla
        $users = \Illuminate\Support\Facades\DB::table('users')->get();
        foreach ($users as $user) {
            if (empty($user->nombre)) {
                $parts = explode(' ', $user->name, 2);
                $nombre = $parts[0];
                $apellido = $parts[1] ?? '';

                \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                ]);
            }
        }

        // 2. Eliminar la columna name
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
        });

        // Restaurar name a partir de nombre y apellido
        $users = \Illuminate\Support\Facades\DB::table('users')->get();
        foreach ($users as $user) {
            \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update([
                'name' => trim($user->nombre . ' ' . $user->apellido),
            ]);
        }
    }
};
