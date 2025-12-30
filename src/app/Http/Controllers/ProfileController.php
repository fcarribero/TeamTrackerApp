<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
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
