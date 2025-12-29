<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $teamName = Setting::get('team_name');
        return view('profesor.settings.index', compact('teamName'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_name' => 'nullable|string|max:255'
        ]);

        Setting::set('team_name', $request->team_name);

        return redirect()->back()->with('success', 'Configuración actualizada correctamente');
    }
}
