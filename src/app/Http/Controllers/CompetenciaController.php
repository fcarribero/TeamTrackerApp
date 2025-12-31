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

        $profesorId = session('active_profesor_id');
        $competencias = Competencia::where('alumno_id', $alumno->id);

        if ($profesorId) {
            $competencias->where('profesorId', $profesorId);
        }

        $competencias = $competencias->orderBy('fecha', 'asc')->get();

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
            'ubicación' => 'nullable|string|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        Competencia::create([
            'id' => 'comp' . bin2hex(random_bytes(10)),
            'alumno_id' => $alumno->id,
            'profesorId' => session('active_profesor_id'),
            'nombre' => $request->nombre,
            'fecha' => $request->fecha,
            'ubicación' => $request->ubicación,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
        ]);

        return redirect()->route('alumno.competencias')->with('success', 'Competencia cargada correctamente');
    }

    public function editAlumno(Competencia $competencia)
    {
        $user = auth()->user();
        $alumno = Alumno::where('userId', $user->id)->first();

        if (!$alumno || $competencia->alumno_id !== $alumno->id) {
            return redirect()->route('alumno.competencias')->with('error', 'No tienes permiso para editar esta competencia');
        }

        return view('alumno.competencias.edit', compact('competencia'));
    }

    public function updateAlumno(Request $request, Competencia $competencia)
    {
        $user = auth()->user();
        $alumno = Alumno::where('userId', $user->id)->first();

        if (!$alumno || $competencia->alumno_id !== $alumno->id) {
            return redirect()->route('alumno.competencias')->with('error', 'No tienes permiso para editar esta competencia');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'ubicación' => 'nullable|string|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'resultado_obtenido' => 'nullable|string',
        ]);

        $competencia->update($request->only([
            'nombre',
            'fecha',
            'ubicación',
            'latitud',
            'longitud',
            'resultado_obtenido',
        ]));

        return redirect()->route('alumno.competencias')->with('success', 'Competencia actualizada correctamente');
    }

    public function destroyAlumno(Competencia $competencia)
    {
        $user = auth()->user();
        $alumno = Alumno::where('userId', $user->id)->first();

        if (!$alumno || $competencia->alumno_id !== $alumno->id) {
            return redirect()->route('alumno.competencias')->with('error', 'No tienes permiso para eliminar esta competencia');
        }

        $competencia->delete();
        return redirect()->route('alumno.competencias')->with('success', 'Competencia eliminada');
    }

    // --- Profesor Methods ---

    public function indexProfesor()
    {
        $profesorId = auth()->id();
        $competencias = Competencia::where('profesorId', $profesorId)
            ->with('alumno')
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
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'plan_carrera' => 'nullable|string',
            'tiempo_objetivo' => 'nullable|string|max:255',
            'resultado_obtenido' => 'nullable|string',
            'ubicación' => 'nullable|string|max:255',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        $competencia->update($request->only([
            'fecha',
            'observaciones',
            'plan_carrera',
            'tiempo_objetivo',
            'resultado_obtenido',
            'ubicación',
            'latitud',
            'longitud',
        ]));

        return redirect()->route('competencias.index')->with('success', 'Información de competencia actualizada');
    }

    public function destroy(Competencia $competencia)
    {
        $competencia->delete();
        return redirect()->back()->with('success', 'Competencia eliminada');
    }
}
