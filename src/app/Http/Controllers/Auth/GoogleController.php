<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Actualizar google_id si no lo tiene
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }

                // Actualizar imagen si no tiene una local
                if (!$user->image && $googleUser->avatar) {
                    $user->update(['image' => $googleUser->avatar]);
                }

                Auth::login($user);
            } else {
                // Si el usuario no existe, lo creamos con rol 'alumno' por defecto
                $userId = 'cl' . bin2hex(random_bytes(10));

                $parts = explode(' ', $googleUser->name, 2);
                $nombre = $parts[0];
                $apellido = $parts[1] ?? '';

                $user = User::create([
                    'id' => $userId,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'rol' => 'alumno', // Rol por defecto
                    'image' => $googleUser->avatar,
                    'password' => null,
                    'fechaNacimiento' => now(),
                    'sexo' => 'otro',
                ]);

                Auth::login($user);
            }

            if ($user->rol === 'profesor') {
                return redirect()->intended('/dashboard/profesor');
            } else {
                return redirect()->intended('/dashboard/alumno');
            }

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Error al autenticar con Google: ' . $e->getMessage());
        }
    }
}
