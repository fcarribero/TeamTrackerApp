<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $teamName = Setting::get('team_name');
        $teamLogo = Setting::get('team_logo');
        $user = auth()->user();
        return view('profesor.settings.index', compact('teamName', 'teamLogo', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_name' => 'nullable|string|max:255',
            'team_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'notify_invitation_accepted' => 'nullable|in:0,1',
        ]);

        Setting::set('team_name', $request->team_name);

        if ($request->hasFile('team_logo')) {
            $path = $request->file('team_logo')->store('logos', 'public');
            Setting::set('team_logo', $path);
        }

        if ($request->has('notify_invitation_accepted')) {
            Setting::set('notify_invitation_accepted', $request->notify_invitation_accepted);
        }

        $user = auth()->user();
        $user->name = $request->name;
        $user->ciudad = $request->ciudad;
        $user->latitud = $request->latitud;
        $user->longitud = $request->longitud;
        $user->save();

        return redirect()->back()->with('success', 'Configuración actualizada correctamente');
    }
}
