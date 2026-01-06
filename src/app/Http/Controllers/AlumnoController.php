<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AlumnoService;
use App\Services\GrupoService;
use App\Services\EntrenamientoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumnoController extends Controller
{
    protected $alumnoService;
    protected $grupoService;
    protected $entrenamientoService;

    public function __construct(AlumnoService $alumnoService, GrupoService $grupoService, EntrenamientoService $entrenamientoService)
    {
        $this->alumnoService = $alumnoService;
        $this->grupoService = $grupoService;
        $this->entrenamientoService = $entrenamientoService;
    }

    public function index(Request $request)
    {
        $profesorId = auth()->id();
        $search = $request->input('search');

        $query = auth()->user()->alumnos()->with(['grupos' => function($q) use ($profesorId) {
            $q->where('profesorId', $profesorId);
        }]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere(\Illuminate\Support\Facades\DB::raw("CONCAT(nombre, ' ', apellido)"), 'like', "%{$search}%");
            });
        }

        $alumnos = $query->get();

        return view('profesor.alumnos.index', compact('alumnos'));
    }

    public function search(Request $request)
    {
        $search = $request->input('q');
        $ids = $request->input('ids');
        $profesorId = auth()->id();

        $query = auth()->user()->alumnos();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere(\Illuminate\Support\Facades\DB::raw("CONCAT(nombre, ' ', apellido)"), 'like', "%{$search}%");
            });
        }

        if ($ids) {
            $idArray = explode(',', $ids);
            $query->whereIn('users.id', $idArray);
        }

        $alumnos = $query->limit(10)->get()->map(function($alumno) {
            return [
                'id' => $alumno->id,
                'nombre' => $alumno->nombre,
                'apellido' => $alumno->apellido,
                'nombre_completo' => $alumno->nombre . ' ' . $alumno->apellido,
                'image' => $alumno->image ? asset('storage/' . $alumno->image) : null,
                'inicial' => substr($alumno->nombre, 0, 1)
            ];
        });

        return response()->json($alumnos);
    }

    public function show(Request $request, $id)
    {
        $alumno = $this->alumnoService->getAlumnoWithDetails($id);
        if (!$alumno) {
            return redirect()->route('alumnos.index')->with('error', 'Alumno no encontrado');
        }
        $entrenamientos = $this->entrenamientoService->getForAlumno($id);
        return view('profesor.alumnos.show', compact('alumno', 'entrenamientos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dni' => 'required|string|max:20',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fechaNacimiento' => 'required|date',
            'notas' => 'nullable|string',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id'
        ]);

        $alumno = $this->alumnoService->createAlumno($data);

        // Vincular con el profesor actual
        $alumno->profesores()->syncWithoutDetaching([auth()->id()]);

        // Sincronizar grupos (solo los que el profesor maneja)
        if (!empty($data['grupos'])) {
            $managedGroupIds = auth()->user()->gruposManaged()->pluck('id')->toArray();
            $validGroups = array_intersect($data['grupos'], $managedGroupIds);
            $alumno->grupos()->sync($validGroups);
        }

        return redirect()->route('alumnos.index')->with('success', 'Alumno creado y vinculado correctamente');
    }

    public function edit($id)
    {
        $alumno = $this->alumnoService->getAlumnoById($id);
        if (!$alumno) {
            return redirect()->route('alumnos.index')->with('error', 'Alumno no encontrado');
        }
        $grupos = auth()->user()->gruposManaged;
        return view('profesor.alumnos.edit', compact('alumno', 'grupos'));
    }

    public function update(Request $request, $id)
    {
        $alumno = $this->alumnoService->getAlumnoById($id);
        if (!$alumno) {
            return redirect()->route('alumnos.index')->with('error', 'Alumno no encontrado');
        }

        $data = $request->validate([
            'notas' => 'nullable|string',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id'
        ]);

        $this->alumnoService->updateAlumno($id, ['notas' => $data['notas'] ?? $alumno->notas]);

        // Solo sincronizar con los grupos que el profesor maneja
        $managedGroupIds = auth()->user()->gruposManaged()->pluck('id')->toArray();
        $requestedGroups = $request->grupos ?? [];

        // Mantener grupos que NO son del profesor y actualizar los que SÍ son
        $otherGroupIds = $alumno->grupos()->whereNotIn('grupos.id', $managedGroupIds)->pluck('grupos.id')->toArray();
        $newGroups = array_merge($otherGroupIds, array_intersect($requestedGroups, $managedGroupIds));

        $alumno->grupos()->sync($newGroups);

        return redirect()->route('alumnos.index')->with('success', 'Asignación de grupos actualizada correctamente');
    }

    public function destroy($id)
    {
        $alumno = User::where('rol', 'alumno')->findOrFail($id);
        $profesorId = auth()->id();
        $managedGroupIds = auth()->user()->gruposManaged()->pluck('id');

        // Desvincular de los grupos de este profesor
        $alumno->grupos()->detach($managedGroupIds);

        // Desvincular del profesor (Equipo)
        $alumno->profesores()->detach($profesorId);

        return redirect()->route('alumnos.index')->with('success', 'Alumno removido de tus grupos y equipo.');
    }

    public function configuracion()
    {
        $alumno = auth()->user();
        if (!$alumno->isAlumno()) {
            return redirect()->route('login');
        }

        return view('alumno.configuracion', compact('alumno'));
    }
}
