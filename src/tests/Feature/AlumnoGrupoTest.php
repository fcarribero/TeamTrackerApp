<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumnoGrupoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_groups_to_alumno()
    {
        $alumno = Alumno::create([
            'id' => 'alumno-1',
            'nombre' => 'Test Alumno',
            'fechaNacimiento' => '2000-01-01',
            'sexo' => 'masculino',
        ]);

        $grupo = Grupo::create([
            'id' => 'grupo-1',
            'nombre' => 'Test Grupo',
        ]);

        // Esto debería fallar si la tabla grupos_alumnos requiere un 'id' que no se provee
        $alumno->grupos()->sync([$grupo->id]);

        $this->assertCount(1, $alumno->grupos);
        $this->assertEquals($grupo->id, $alumno->grupos->first()->id);
    }
}
