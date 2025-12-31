<?php

namespace App\Mail;

use App\Models\Invitacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitacionGrupo extends Mailable
{
    use Queueable, SerializesModels;

    public $invitacion;
    public $existeUsuario;

    public function __construct(Invitacion $invitacion, bool $existeUsuario)
    {
        $this->invitacion = $invitacion;
        $this->existeUsuario = $existeUsuario;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitación a unirse a un grupo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitacion',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
