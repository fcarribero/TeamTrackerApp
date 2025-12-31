<?php

namespace Tests\Feature;

use App\Models\Entrenamiento;
use App\Models\PlantillaEntrenamiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntrenamientoTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_change_template_once_set()
    {
        $user = User::create([
            'id' => 'user-1',
            'name' => 'Profesor',
            'email' => 'profesor@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $plantilla1 = PlantillaEntrenamiento::create([
            'id' => 'p1',
            'nombre' => 'Plantilla 1',
            'contenido' => 'Contenido 1'
        ]);

        $plantilla2 = PlantillaEntrenamiento::create([
            'id' => 'p2',
            'nombre' => 'Plantilla 2',
            'contenido' => 'Contenido 2'
        ]);

        $entrenamiento = Entrenamiento::create([
            'id' => 'e1',
            'titulo' => 'Entrenamiento Test',
            'fecha' => now(),
            'plantillaId' => $plantilla1->id,
            'plantillaNombre' => $plantilla1->nombre,
        ]);

        $response = $this->put(route('entrenamientos.update', $entrenamiento->id), [
            'titulo' => 'Entrenamiento Modificado',
            'fecha' => now()->format('Y-m-d'),
            'plantillaId' => $plantilla2->id, // Intento cambiar a plantilla 2
        ]);

        $response->assertRedirect(route('entrenamientos.index'));

        $entrenamiento->refresh();
        $this->assertEquals($plantilla1->id, $entrenamiento->plantillaId);
        $this->assertEquals($plantilla1->nombre, $entrenamiento->plantillaNombre);
        $this->assertEquals('Entrenamiento Modificado', $entrenamiento->titulo);
    }

    public function test_can_set_template_if_it_was_null()
    {
        $user = User::create([
            'id' => 'user-2',
            'name' => 'Profesor 2',
            'email' => 'profesor2@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $plantilla1 = PlantillaEntrenamiento::create([
            'id' => 'p1',
            'nombre' => 'Plantilla 1',
            'contenido' => 'Contenido 1'
        ]);

        $entrenamiento = Entrenamiento::create([
            'id' => 'e1',
            'titulo' => 'Entrenamiento Sin Plantilla',
            'fecha' => now(),
            'plantillaId' => null,
        ]);

        $response = $this->put(route('entrenamientos.update', $entrenamiento->id), [
            'titulo' => 'Entrenamiento Con Plantilla',
            'fecha' => now()->format('Y-m-d'),
            'plantillaId' => $plantilla1->id,
        ]);

        $response->assertRedirect(route('entrenamientos.index'));

        $entrenamiento->refresh();
        $this->assertEquals($plantilla1->id, $entrenamiento->plantillaId);
        $this->assertEquals($plantilla1->nombre, $entrenamiento->plantillaNombre);
    }
}
