<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Añadir columnas a users
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('name');
            $table->string('apellido')->nullable()->after('nombre');
            $table->string('dni')->nullable()->after('apellido');
            $table->timestamp('fechaNacimiento')->nullable();
            $table->string('sexo')->nullable();
            $table->string('obra_social')->nullable();
            $table->string('numero_socio')->nullable();
            $table->string('certificado_medico')->nullable();
            $table->date('vencimiento_certificado')->nullable();
            $table->text('notas')->nullable();
        });

        if (!Schema::hasTable('alumnos')) {
            return;
        }

        // 2. Migrar datos de alumnos a users
        $alumnos = DB::table('alumnos')->get();
        $alumnoIdToUserId = [];

        foreach ($alumnos as $alumno) {
            $userId = $alumno->userId;

            if (!$userId) {
                // Generar un ID único si no tiene uno
                $userId = 'u' . Str::random(10);
                while (DB::table('users')->where('id', $userId)->exists()) {
                    $userId = 'u' . Str::random(10);
                }

                // Generar un email único basado en el ID para evitar colisiones
                $email = 'alumno_' . $alumno->id . '@teamtracker.com';

                DB::table('users')->insert([
                    'id' => $userId,
                    'name' => $alumno->nombre . ' ' . $alumno->apellido,
                    'nombre' => $alumno->nombre,
                    'apellido' => $alumno->apellido,
                    'email' => $email,
                    'password' => bcrypt(Str::random(16)),
                    'rol' => 'alumno',
                    'dni' => $alumno->dni,
                    'fechaNacimiento' => $alumno->fechaNacimiento,
                    'sexo' => $alumno->sexo,
                    'obra_social' => $alumno->obra_social,
                    'numero_socio' => $alumno->numero_socio,
                    'certificado_medico' => $alumno->certificado_medico,
                    'vencimiento_certificado' => $alumno->vencimiento_certificado,
                    'notas' => $alumno->notas,
                    'created_at' => $alumno->created_at,
                    'updated_at' => $alumno->updated_at,
                ]);
            } else {
                // Actualizar usuario existente
                DB::table('users')->where('id', $userId)->update([
                    'nombre' => $alumno->nombre,
                    'apellido' => $alumno->apellido,
                    'dni' => $alumno->dni,
                    'fechaNacimiento' => $alumno->fechaNacimiento,
                    'sexo' => $alumno->sexo,
                    'obra_social' => $alumno->obra_social,
                    'numero_socio' => $alumno->numero_socio,
                    'certificado_medico' => $alumno->certificado_medico,
                    'vencimiento_certificado' => $alumno->vencimiento_certificado,
                    'notas' => $alumno->notas,
                    'rol' => 'alumno', // Forzar rol alumno si ya no lo tenía
                ]);
            }
            $alumnoIdToUserId[$alumno->id] = $userId;
        }

        // 3. Actualizar tablas relacionadas
        $this->updateRelatedTables($alumnoIdToUserId);

        // 4. Eliminar tabla alumnos
        Schema::dropIfExists('alumnos');
    }

    private function updateRelatedTables(array $map)
    {
        // Mapeo de tablas, columnas y nombres de foreign keys (según migraciones)
        $configs = [
            'grupos_alumnos' => ['alumnoId', 'grupos_alumnos_alumnoid_foreign'],
            'pagos' => ['alumnoId', 'pagos_alumnoid_foreign'],
            'entrenamientos_alumnos' => ['alumnoId', 'entrenamientos_alumnos_alumnoid_foreign'],
            'objetivos_competencias' => ['alumnoId', 'objetivos_competencias_alumnoid_foreign'],
            'competencias' => ['alumno_id', 'competencias_alumno_id_foreign'],
            'entrenamiento_resultados' => ['alumnoId', 'entrenamiento_resultados_alumnoid_foreign'],
            'garmin_accounts' => ['alumno_id', 'garmin_accounts_alumno_id_foreign'],
            'garmin_activities' => ['alumno_id', 'garmin_activities_alumno_id_foreign'],
            'profesor_alumno' => ['alumno_id', 'profesor_alumno_alumno_id_foreign'],
        ];

        foreach ($configs as $table => $info) {
            if (!Schema::hasTable($table)) continue;

            $column = $info[0];
            $foreign = $info[1];

            // Intentar dropear la foreign key
            try {
                Schema::table($table, function (Blueprint $t) use ($foreign) {
                    $t->dropForeign($foreign);
                });
            } catch (\Exception $e) {
                // Si falla por el nombre, intentamos el nombre alternativo o simplemente logueamos
            }

            // Actualizar datos
            foreach ($map as $oldId => $newId) {
                DB::table($table)->where($column, $oldId)->update([$column => $newId]);
            }

            // Crear nueva foreign key apuntando a users
            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->foreign($column)->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // No es fácil de revertir completamente sin perder datos de la unión
    }
};
