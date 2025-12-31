<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Invitacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitacionRegistroTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumno_invitado_se_registra_y_queda_verificado()
    {
        // 1. Crear un profesor
        $profesor = User::create([
            'id' => 'prof-1',
            'name' => 'Profesor Test',
            'email' => 'prof@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
        ]);

        // 2. Crear una invitación
        $invitacion = Invitacion::create([
            'email' => 'alumno@test.com',
            'profesorId' => $profesor->id,
            'token' => 'test-token',
            'status' => 'pending',
        ]);

        // 3. Simular el registro vía signup
        $response = $this->post('/signup', [
            'nombre' => 'Alumno',
            'apellido' => 'Test',
            'dni' => '12345678',
            'fechaNacimiento' => '2000-01-01',
            'sexo' => 'masculino',
            'obra_social' => 'OSDE',
            'email' => 'alumno@test.com',
            'password' => 'password123',
            'rol' => 'alumno',
            'invitation_token' => 'test-token',
        ]);

        $response->assertRedirect('/dashboard/alumno');

        // 4. Verificar que el usuario existe y está verificado
        $user = User::where('email', 'alumno@test.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->email_verified_at, 'El email del alumno invitado debería estar verificado');
    }

    public function test_registro_normal_no_queda_verificado_automaticamente()
    {
        $response = $this->post('/signup', [
            'nombre' => 'User',
            'apellido' => 'Test',
            'dni' => '87654321',
            'fechaNacimiento' => '1995-05-05',
            'sexo' => 'femenino',
            'obra_social' => 'Swiss Medical',
            'email' => 'user@test.com',
            'password' => 'password123',
            'rol' => 'alumno',
        ]);

        $response->assertRedirect('/dashboard/alumno');

        $user = User::where('email', 'user@test.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at, 'El email de un registro normal no debería estar verificado automáticamente');
    }
}
