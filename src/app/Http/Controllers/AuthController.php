<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
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

    public function showSignup()
    {
        return view('auth.signup');
    }

    public function signup(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nombre' => 'required',
            'rol' => 'required|in:profesor,alumno',
        ]);

        $userId = 'cl' . bin2hex(random_bytes(10));

        $user = User::create([
            'id' => $userId,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'name' => $data['nombre'],
            'rol' => $data['rol'],
        ]);

        if ($data['rol'] === 'alumno') {
            Alumno::create([
                'id' => 'cl' . bin2hex(random_bytes(10)),
                'nombre' => $data['nombre'],
                'fechaNacimiento' => now(), // Valor por defecto
                'sexo' => 'otro', // Valor por defecto
                'userId' => $user->id,
            ]);
        }

        Auth::login($user);

        if ($user->rol === 'profesor') {
            return redirect('/dashboard/profesor');
        } else {
            return redirect('/dashboard/alumno');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
