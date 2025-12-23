<?php

namespace App\Http\Controllers;

use App\Services\AlumnoService;
use App\Services\GrupoService;
use App\Services\EntrenamientoService;
use Illuminate\Http\Request;

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
        $alumnos = $this->alumnoService->getAllAlumnos();
        return view('profesor.alumnos.index', compact('alumnos'));
    }

    public function create()
    {
        $grupos = $this->grupoService->getAll();
        return view('profesor.alumnos.create', compact('grupos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'fechaNacimiento' => 'required|date',
            'sexo' => 'required|in:masculino,femenino,otro',
            'notas' => 'nullable|string',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id'
        ]);

        $alumno = $this->alumnoService->createAlumno($data);
        if ($request->has('grupos')) {
            $alumno->grupos()->sync($request->grupos);
        }

        return redirect()->route('alumnos.index')->with('success', 'Alumno creado correctamente');
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

    public function edit($id)
    {
        $alumno = $this->alumnoService->getAlumnoById($id);
        if (!$alumno) {
            return redirect()->route('alumnos.index')->with('error', 'Alumno no encontrado');
        }
        $grupos = $this->grupoService->getAll();
        return view('profesor.alumnos.edit', compact('alumno', 'grupos'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'fechaNacimiento' => 'required|date',
            'sexo' => 'required|in:masculino,femenino,otro',
            'notas' => 'nullable|string',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id'
        ]);

        $this->alumnoService->updateAlumno($id, $data);
        $alumno = $this->alumnoService->getAlumnoById($id);
        $alumno->grupos()->sync($request->grupos ?? []);

        return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado correctamente');
    }

    public function destroy($id)
    {
        $this->alumnoService->deleteAlumno($id);
        return redirect()->route('alumnos.index')->with('success', 'Alumno eliminado correctamente');
    }
}
