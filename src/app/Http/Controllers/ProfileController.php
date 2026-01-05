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

    public function update(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
        ];

        if ($user->rol === 'alumno') {
            $rules['dni'] = 'required|string|max:20';
            $rules['fechaNacimiento'] = 'required|date';
            $rules['sexo'] = 'required|in:masculino,femenino';
            $rules['obra_social'] = 'nullable|string|max:255';
            $rules['numero_socio'] = 'nullable|string|max:255';
            $rules['vencimiento_certificado'] = 'nullable|date';
            $rules['certificado_medico'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        $data = $request->validate($rules);

        if ($request->hasFile('certificado_medico')) {
            if ($user->certificado_medico) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->certificado_medico);
            }
            $path = $request->file('certificado_medico')->store('certificados', 'public');
            $data['certificado_medico'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Perfil actualizado correctamente');
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
