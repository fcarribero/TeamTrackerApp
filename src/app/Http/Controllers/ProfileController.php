<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        if ($user->rol === 'alumno') {
            $user->load('profesores');
        }
        $alumno = $user->rol === 'alumno' ? $user : null;

        return view('profile.show', compact('user', 'alumno'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente');
    }

    public function updateLocation(Request $request)
    {
        $data = $request->validate([
            'ciudad' => 'required|string|max:255',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        $user = Auth::user();
        $user->update([
            'ciudad' => $data['ciudad'],
            'latitud' => $data['latitud'],
            'longitud' => $data['longitud'],
        ]);

        return back()->with('success', 'Ubicación actualizada correctamente');
    }
}
