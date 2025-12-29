<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Competencia;
use Illuminate\Http\Request;

class CompetenciaController extends Controller
{
    // --- Alumno Methods ---

    public function indexAlumno()
    {
        $user = auth()->user();
        $alumno = Alumno::where('userId', $user->id)->first();

        if (!$alumno) {
            return redirect('/')->with('error', 'Perfil de alumno no encontrado');
        }

        $competencias = Competencia::where('alumno_id', $alumno->id)
            ->orderBy('fecha', 'asc')
            ->get();

        return view('alumno.competencias.index', compact('competencias'));
    }

    public function storeAlumno(Request $request)
    {
        $user = auth()->user();
        $alumno = Alumno::where('userId', $user->id)->first();

        if (!$alumno) {
            return redirect()->back()->with('error', 'No se pudo encontrar tu perfil de alumno');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
        ]);

        Competencia::create([
            'id' => 'comp' . bin2hex(random_bytes(10)),
            'alumno_id' => $alumno->id,
            'nombre' => $request->nombre,
            'fecha' => $request->fecha,
        ]);

        return redirect()->route('alumno.competencias')->with('success', 'Competencia cargada correctamente');
    }

    // --- Profesor Methods ---

    public function indexProfesor()
    {
        $competencias = Competencia::with('alumno')
            ->orderBy('fecha', 'asc')
            ->get();

        return view('profesor.competencias.index', compact('competencias'));
    }

    public function editProfesor(Competencia $competencia)
    {
        return view('profesor.competencias.edit', compact('competencia'));
    }

    public function updateProfesor(Request $request, Competencia $competencia)
    {
        $request->validate([
            'observaciones' => 'nullable|string',
            'plan_carrera' => 'nullable|string',
            'tiempo_objetivo' => 'nullable|string|max:255',
            'resultado_obtenido' => 'nullable|string',
        ]);

        $competencia->update($request->only([
            'observaciones',
            'plan_carrera',
            'tiempo_objetivo',
            'resultado_obtenido'
        ]));

        return redirect()->route('competencias.index')->with('success', 'Información de competencia actualizada');
    }

    public function destroy(Competencia $competencia)
    {
        $competencia->delete();
        return redirect()->back()->with('success', 'Competencia eliminada');
    }
}
