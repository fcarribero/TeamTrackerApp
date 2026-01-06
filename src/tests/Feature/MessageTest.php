<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    protected $profesor;
    protected $alumno;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profesor = User::factory()->create([
            'rol' => 'profesor',
            'id' => 'p1'
        ]);

        $this->alumno = User::factory()->create([
            'rol' => 'alumno',
            'id' => 'a1'
        ]);

        // Relacionar profesor y alumno
        $this->profesor->alumnos()->attach($this->alumno->id);
    }

    public function test_alumno_can_send_message_to_profesor()
    {
        $this->actingAs($this->alumno);

        $response = $this->post(route('mensajes.store'), [
            'receiver_id' => $this->profesor->id,
            'content' => 'Hola profesor',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->alumno->id,
            'receiver_id' => $this->profesor->id,
            'content' => 'Hola profesor',
        ]);
    }

    public function test_profesor_can_view_messages_from_alumno()
    {
        Message::create([
            'sender_id' => $this->alumno->id,
            'receiver_id' => $this->profesor->id,
            'content' => 'Hola profesor',
        ]);

        $this->actingAs($this->profesor);

        $response = $this->get(route('mensajes.show', $this->alumno->id));

        $response->assertStatus(200);
        $response->assertSee('Hola profesor');
    }

    public function test_can_send_message_with_attachment()
    {
        Storage::fake('public');
        $this->actingAs($this->alumno);

        $file = UploadedFile::fake()->create('test.pdf', 100);

        $response = $this->post(route('mensajes.store'), [
            'receiver_id' => $this->profesor->id,
            'content' => 'Aquí está el PDF',
            'attachment' => $file,
        ]);

        $response->assertStatus(302);

        $message = Message::first();
        $this->assertNotNull($message->attachment_path);
        Storage::disk('public')->assertExists($message->attachment_path);
        $this->assertEquals('pdf', $message->attachment_type);
    }

    public function test_cannot_send_message_to_non_related_user()
    {
        $otherAlumno = User::factory()->create(['rol' => 'alumno', 'id' => 'a2']);

        $this->actingAs($this->profesor);

        // El profesor no tiene a 'a2' como alumno, por lo que show debería fallar (abort 403)
        $response = $this->get(route('mensajes.show', $otherAlumno->id));
        $response->assertStatus(403);
    }
}
