<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isProfesor()) {
            // Obtener solo los alumnos que tienen mensajes con este profesor
            $alumnos = $user->alumnos()
                ->where(function($query) use ($user) {
                    $query->whereHas('receivedMessages', function($q) use ($user) {
                        $q->where('sender_id', $user->id);
                    })->orWhereHas('sentMessages', function($q) use ($user) {
                        $q->where('receiver_id', $user->id);
                    });
                })
                ->with(['receivedMessages' => function($query) use ($user) {
                    $query->where('sender_id', $user->id)->latest();
                }, 'sentMessages' => function($query) use ($user) {
                    $query->where('receiver_id', $user->id)->latest();
                }])->get()->map(function($alumno) use ($user) {
                $lastSent = $alumno->sentMessages->first();
                $lastReceived = $alumno->receivedMessages->first();

                $lastMessage = null;
                if ($lastSent && $lastReceived) {
                    $lastMessage = $lastSent->created_at > $lastReceived->created_at ? $lastSent : $lastReceived;
                } else {
                    $lastMessage = $lastSent ?? $lastReceived;
                }

                $unreadCount = Message::where('sender_id', $alumno->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                $alumno->last_message = $lastMessage;
                $alumno->unread_count = $unreadCount;
                return $alumno;
            })->sortByDesc(function($alumno) {
                return $alumno->last_message ? $alumno->last_message->created_at : $alumno->created_at;
            });

            return view('profesor.mensajes.index', compact('alumnos'));
        }

        if ($user->isAlumno()) {
            // Para el alumno, mostramos sus profesores
            $profesores = $user->profesores()->get();

            // Si solo tiene uno, lo redirigimos directamente al chat
            if ($profesores->count() === 1) {
                return redirect()->route('mensajes.show', $profesores->first()->id);
            }

            return view('alumno.mensajes.index', compact('profesores'));
        }

        return redirect()->route('dashboard');
    }

    public function show($id)
    {
        $user = Auth::user();
        $otherUser = User::findOrFail($id);

        // Verificar que hay relación profesor-alumno
        if ($user->isProfesor()) {
            if (!$user->alumnos()->where('alumno_id', $id)->exists()) {
                abort(403);
            }
        } elseif ($user->isAlumno()) {
            if (!$user->profesores()->where('profesor_id', $id)->exists()) {
                abort(403);
            }
        }

        // Marcar como leídos
        Message::where('sender_id', $id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function($query) use ($user, $id) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $id);
            })->orWhere(function($query) use ($user, $id) {
                $query->where('sender_id', $id)
                      ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $view = $user->isProfesor() ? 'profesor.mensajes.show' : 'alumno.mensajes.show';

        return view($view, compact('otherUser', 'messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required_without:attachment|nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $user = Auth::user();
        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            $attachmentType = $file->getClientMimeType();

            if (str_contains($attachmentType, 'image')) {
                $attachmentType = 'image';
            } elseif (str_contains($attachmentType, 'pdf')) {
                $attachmentType = 'pdf';
            }
        }

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->input('content'),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
        ]);

        return back()->with('success', 'Mensaje enviado correctamente.');
    }
}
