<?php

namespace App\Http\Controllers;

use App\Models\Invitacion;
use App\Models\User;
use App\Models\Alumno;
use App\Mail\InvitacionGrupo;
use App\Mail\NotificarAceptacionInvitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitacionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'grupo_id' => 'nullable|exists:grupos,id',
        ]);

        $profesor = auth()->user();

        // Verificar que no sea profesor
        $user = User::where('email', $request->email)->first();
        if ($user && $user->rol === 'profesor') {
            return back()->with('error', 'No puedes invitar a otro profesor.');
        }

        $invitacion = Invitacion::create([
            'email' => $request->email,
            'profesorId' => $profesor->id,
            'grupoId' => $request->grupo_id,
            'token' => Str::random(32),
            'status' => 'pending',
        ]);

        Mail::to($request->email)->send(new InvitacionGrupo($invitacion, (bool)$user));

        return back()->with('success', 'Invitación enviada.');
    }

    public function aceptar($token)
    {
        $invitacion = Invitacion::where('token', $token)->where('status', 'pending')->firstOrFail();

        if (!auth()->check()) {
            $userExists = User::where('email', $invitacion->email)->exists();
            if ($userExists) {
                // Guardamos la URL actual para redirigir aquí después del login
                session(['url.intended' => route('invitaciones.aceptar', $token)]);
                return redirect()->route('login', ['email' => $invitacion->email])->with('info', 'Ya tienes una cuenta. Por favor inicia sesión para aceptar la invitación.');
            }
            return redirect()->route('signup', ['invitation_token' => $token]);
        }

        $user = auth()->user();

        if ($user->email !== $invitacion->email) {
            return redirect('/dashboard')->with('error', 'Este enlace de invitación no es para tu usuario.');
        }

        // Enlazar alumno con profesor/grupo
        $alumno = $user->alumno;
        if (!$alumno) {
            $alumno = Alumno::create([
                'id' => 'cl' . bin2hex(random_bytes(10)),
                'nombre' => $user->name,
                'userId' => $user->id,
                'fechaNacimiento' => now(),
                'sexo' => 'otro',
            ]);
        }

        if ($invitacion->grupoId) {
            $alumno->grupos()->syncWithoutDetaching([$invitacion->grupoId]);
        }

        // Vincular con el profesor (Equipo)
        $alumno->profesores()->syncWithoutDetaching([$invitacion->profesorId]);

        $invitacion->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Notificar al profesor
        if ($invitacion->profesor) {
            $notify = \App\Models\Setting::get('notify_invitation_accepted', '1', $invitacion->profesorId);
            if ($notify === '1') {
                Mail::to($invitacion->profesor->email)->send(new NotificarAceptacionInvitacion($user, $invitacion));
            }
        }

        return redirect('/dashboard/alumno')->with('success', 'Te has unido al grupo.');
    }
}
