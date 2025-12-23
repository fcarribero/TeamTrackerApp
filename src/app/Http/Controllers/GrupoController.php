<?php
namespace App\Http\Controllers;
use App\Services\GrupoService;
use App\Services\AlumnoService;
use Illuminate\Http\Request;
class GrupoController extends Controller {
    protected $service;
    protected $alumnoService;
    public function __construct(GrupoService $service, AlumnoService $alumnoService) {
        $this->service = $service;
        $this->alumnoService = $alumnoService;
    }

    public function index() {
        $grupos = $this->service->getAll();
        return view('profesor.grupos.index', compact('grupos'));
    }

    public function create() {
        $alumnos = $this->alumnoService->getAllAlumnos();
        return view('profesor.grupos.create', compact('alumnos'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'alumnos' => 'nullable|array',
            'alumnos.*' => 'exists:alumnos,id'
        ]);

        $grupo = $this->service->create($data);
        if ($request->has('alumnos')) {
            $grupo->alumnos()->sync($request->alumnos);
        }

        return redirect()->route('grupos.index')->with('success', 'Grupo creado correctamente');
    }

    public function edit($id) {
        $grupo = $this->service->find($id);
        if (!$grupo) return redirect()->route('grupos.index')->with('error', 'Grupo no encontrado');

        $alumnos = $this->alumnoService->getAllAlumnos();
        return view('profesor.grupos.edit', compact('grupo', 'alumnos'));
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'alumnos' => 'nullable|array',
            'alumnos.*' => 'exists:alumnos,id'
        ]);

        $this->service->update($id, $data);
        $grupo = $this->service->find($id);
        $grupo->alumnos()->sync($request->alumnos ?? []);

        return redirect()->route('grupos.index')->with('success', 'Grupo actualizado correctamente');
    }

    public function destroy($id) {
        $this->service->delete($id);
        return redirect()->route('grupos.index')->with('success', 'Grupo eliminado correctamente');
    }
}
