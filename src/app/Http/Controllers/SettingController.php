<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $teamName = Setting::get('team_name');
        $user = auth()->user();
        return view('profesor.settings.index', compact('teamName', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_name' => 'nullable|string|max:255',
            'name' => 'required|string|max:255'
        ]);

        Setting::set('team_name', $request->team_name);

        $user = auth()->user();
        $user->name = $request->name;
        $user->save();

        return redirect()->back()->with('success', 'Configuración actualizada correctamente');
    }
}
