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
        // Asegurarnos de que el índice único esté en (userId, key) y no solo en key
        $indexes = Schema::getIndexes('settings');
        $oldIndexName = 'settings_key_unique';
        $newIndexName = 'settings_userid_key_unique';

        $hasOldIndex = collect($indexes)->contains('name', $oldIndexName);
        $hasNewIndex = collect($indexes)->contains('name', $newIndexName);

        Schema::table('settings', function (Blueprint $table) use ($hasOldIndex, $hasNewIndex, $oldIndexName, $newIndexName) {
            if ($hasOldIndex) {
                $table->dropUnique($oldIndexName);
            }

            if (!$hasNewIndex) {
                // Primero asegurarnos de que no haya duplicados que impidan crear el índice
                // Aunque en este caso, si venimos de un índice único en 'key',
                // no debería haber duplicados en (userId, key) a menos que userId sea NULL.
                $table->unique(['userId', 'key'], $newIndexName);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_userid_key_unique');
            $table->unique('key', 'settings_key_unique');
        });
    }
};
