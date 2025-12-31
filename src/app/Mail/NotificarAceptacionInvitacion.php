<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Invitacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificarAceptacionInvitacion extends Mailable
{
    use Queueable, SerializesModels;

    public $alumno;
    public $invitacion;

    /**
     * Create a new message instance.
     */
    public function __construct(User $alumno, Invitacion $invitacion)
    {
        $this->alumno = $alumno;
        $this->invitacion = $invitacion;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Un alumno se ha unido a tu equipo',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitacion_aceptada',
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
