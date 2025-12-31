<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alumno;
use App\Models\Invitacion;
use App\Mail\NotificarAceptacionInvitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $email = $request->query('email');
        return view('auth.login', compact('email'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->rol === 'alumno') {
                $alumno = $user->alumno;
                if ($alumno) {
                    $profesorIds = $alumno->profesores()->pluck('users.id');
                    if ($profesorIds->count() > 1) {
                        return redirect()->route('grupos.seleccionar');
                    } elseif ($profesorIds->count() === 1) {
                        session(['active_profesor_id' => $profesorIds->first()]);
                    }
                }
            }

            if ($user->rol === 'profesor') {
                return redirect()->intended('/dashboard/profesor');
            } else {
                return redirect()->intended('/dashboard/alumno');
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showSignup(Request $request)
    {
        $invitation_token = $request->query('invitation_token');
        $email = '';
        if ($invitation_token) {
            $invitacion = Invitacion::where('token', $invitation_token)->first();
            if ($invitacion) {
                $email = $invitacion->email;
            }
        }
        return view('auth.signup', compact('invitation_token', 'email'));
    }

    public function signup(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nombre' => 'required|string|max:255',
            'rol' => 'required|in:profesor,alumno',
            'invitation_token' => 'nullable|exists:invitaciones,token',
            // Campos adicionales para alumno
            'apellido' => 'required_if:rol,alumno|string|max:255',
            'dni' => 'required_if:rol,alumno|string|max:20',
            'fechaNacimiento' => 'required_if:rol,alumno|date',
            'sexo' => 'required_if:rol,alumno|in:masculino,femenino,otro',
            'obra_social' => 'required_if:rol,alumno|string|max:255',
        ]);

        $userId = 'cl' . bin2hex(random_bytes(10));

        $user = User::create([
            'id' => $userId,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'name' => $data['rol'] === 'alumno' ? $data['nombre'] . ' ' . $data['apellido'] : $data['nombre'],
            'rol' => $data['rol'],
        ]);

        if ($data['rol'] === 'alumno') {
            $alumno = Alumno::create([
                'id' => 'cl' . bin2hex(random_bytes(10)),
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'dni' => $data['dni'],
                'fechaNacimiento' => $data['fechaNacimiento'],
                'sexo' => $data['sexo'],
                'obra_social' => $data['obra_social'],
                'userId' => $user->id,
            ]);

            if (!empty($data['invitation_token'])) {
                $invitacion = Invitacion::where('token', $data['invitation_token'])->first();
                if ($invitacion && $invitacion->email === $user->email) {
                    // Si viene de una invitación, marcamos el email como verificado
                    $user->markEmailAsVerified();

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
                }
            }
        }

        event(new Registered($user));

        Auth::login($user);

        if ($user->rol === 'profesor') {
            // Crear un grupo por defecto para el profesor
            \App\Models\Grupo::create([
                'id' => 'cl' . bin2hex(random_bytes(10)),
                'nombre' => 'General',
                'descripcion' => 'Grupo principal',
                'profesorId' => $user->id,
            ]);
            return redirect('/dashboard/profesor');
        } else {
            return redirect('/dashboard/alumno');
        }
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
