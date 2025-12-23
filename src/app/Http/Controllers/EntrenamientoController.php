<?php
namespace App\Http\Controllers;
use App\Services\EntrenamientoService;
use App\Services\AlumnoService;
use App\Services\GrupoService;
use App\Services\PlantillaService;
use Illuminate\Http\Request;

class EntrenamientoController extends Controller {
    protected $service;
    protected $alumnoService;
    protected $grupoService;
    protected $plantillaService;

    public function __construct(
        EntrenamientoService $service,
        AlumnoService $alumnoService,
        GrupoService $grupoService,
        PlantillaService $plantillaService
    ) {
        $this->service = $service;
        $this->alumnoService = $alumnoService;
        $this->grupoService = $grupoService;
        $this->plantillaService = $plantillaService;
    }

    public function index() {
        $entrenamientos = $this->service->getAll();
        return view('profesor.entrenamientos.index', compact('entrenamientos'));
    }

    public function show($id) {
        $entrenamiento = $this->service->getWithResultados($id);
        if (!$entrenamiento) return redirect()->route('entrenamientos.index')->with('error', 'Entrenamiento no encontrado');
        return view('profesor.entrenamientos.show', compact('entrenamiento'));
    }

    public function create(Request $request) {
        $alumnos = $this->alumnoService->getAllAlumnos();
        $grupos = $this->grupoService->getAll();
        $plantillas = $this->plantillaService->getAll();
        $selectedPlantilla = null;

        if ($plantillas->isEmpty()) {
            return view('profesor.entrenamientos.create', compact('alumnos', 'grupos', 'plantillas', 'selectedPlantilla'));
        }

        if (!$request->has('plantilla_id') && !$request->has('skip_plantilla')) {
            return view('profesor.entrenamientos.selector', compact('plantillas'));
        }

        if ($request->has('plantilla_id')) {
            $selectedPlantilla = $this->plantillaService->find($request->plantilla_id);
        }

        return view('profesor.entrenamientos.create', compact('alumnos', 'grupos', 'plantillas', 'selectedPlantilla'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha' => 'required|date',
            'alumnos' => 'nullable|array',
            'alumnos.*' => 'exists:alumnos,id',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id',
            'plantillaId' => 'nullable|string|exists:plantillas_entrenamiento,id',
            'contenidoPersonalizado' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        if ($request->filled('contenidoPersonalizado')) {
            $data['contenidoPersonalizado'] = json_decode($data['contenidoPersonalizado'], true);
        }

        if ($request->filled('plantillaId')) {
            $plantilla = $this->plantillaService->find($request->plantillaId);
            $data['plantillaNombre'] = $plantilla->nombre;

            // Si las observaciones están vacías, usamos las de la plantilla
            if (empty($data['observaciones'])) {
                $data['observaciones'] = $plantilla->observaciones;
            }

            // Si el contenido personalizado está vacío o es exactamente el contenido por defecto,
            // usamos el contenido de la plantilla si se ha seleccionado una.
            $esPorDefecto = is_array($data['contenidoPersonalizado'] ?? null) &&
                count($data['contenidoPersonalizado']['calentamiento'] ?? []) === 1 &&
                ($data['contenidoPersonalizado']['calentamiento'][0] ?? '') === "20' CCL" &&
                empty($data['contenidoPersonalizado']['trabajo_principal'] ?? []) &&
                count($data['contenidoPersonalizado']['enfriamiento'] ?? []) === 1 &&
                ($data['contenidoPersonalizado']['enfriamiento'][0] ?? '') === "10' CCL";

            if (empty($data['contenidoPersonalizado']) ||
                (is_array($data['contenidoPersonalizado']) &&
                 empty($data['contenidoPersonalizado']['calentamiento']) &&
                 empty($data['contenidoPersonalizado']['trabajo_principal']) &&
                 empty($data['contenidoPersonalizado']['enfriamiento'])) ||
                $esPorDefecto) {
                $data['contenidoPersonalizado'] = $plantilla->contenido;
            }
        }

        $entrenamiento = $this->service->create($data);

        if ($request->has('alumnos')) {
            $entrenamiento->alumnos()->sync($request->alumnos);
        }

        if ($request->has('grupos')) {
            $entrenamiento->grupos()->sync($request->grupos);
        }

        return redirect()->route('entrenamientos.index')->with('success', 'Entrenamiento programado correctamente');
    }

    public function edit($id) {
        $entrenamiento = $this->service->find($id);
        if (!$entrenamiento) return redirect()->route('entrenamientos.index')->with('error', 'Entrenamiento no encontrado');

        $alumnos = $this->alumnoService->getAllAlumnos();
        $grupos = $this->grupoService->getAll();
        $plantillas = $this->plantillaService->getAll();
        return view('profesor.entrenamientos.edit', compact('entrenamiento', 'alumnos', 'grupos', 'plantillas'));
    }

    public function update(Request $request, $id) {
        $entrenamiento = $this->service->find($id);
        if (!$entrenamiento) return redirect()->route('entrenamientos.index')->with('error', 'Entrenamiento no encontrado');

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha' => 'required|date',
            'alumnos' => 'nullable|array',
            'alumnos.*' => 'exists:alumnos,id',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id',
            'plantillaId' => 'nullable|string|exists:plantillas_entrenamiento,id',
            'contenidoPersonalizado' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        if ($request->filled('contenidoPersonalizado')) {
            $data['contenidoPersonalizado'] = json_decode($data['contenidoPersonalizado'], true);
        }

        // Si ya existen devoluciones, no permitimos cambiar fecha ni ejercicios
        if ($entrenamiento->resultados()->exists()) {
            unset($data['fecha']);
            unset($data['contenidoPersonalizado']);
        }

        // Si ya tenía una plantilla, no permitimos cambiarla
        if ($entrenamiento->plantillaId) {
            unset($data['plantillaId']);
        } elseif ($request->filled('plantillaId')) {
            $plantilla = $this->plantillaService->find($request->plantillaId);
            $data['plantillaNombre'] = $plantilla->nombre;
        }

        $this->service->update($id, $data);

        $entrenamiento->alumnos()->sync($request->alumnos ?? []);
        $entrenamiento->grupos()->sync($request->grupos ?? []);

        return redirect()->route('entrenamientos.index')->with('success', 'Entrenamiento actualizado correctamente');
    }

    public function destroy($id) {
        $this->service->delete($id);
        return redirect()->route('entrenamientos.index')->with('success', 'Entrenamiento eliminado correctamente');
    }

    public function indexAlumno() {
        $user = auth()->user();
        $alumno = \App\Models\Alumno::where('userId', $user->id)->first();
        if (!$alumno) return redirect('/')->with('error', 'Perfil de alumno no encontrado');

        $entrenamientos = $this->service->getForAlumno($alumno->id);
        return view('alumno.entrenamientos.index', compact('entrenamientos'));
    }

    public function completarAlumno(Request $request, $id) {
        $user = auth()->user();
        $alumno = \App\Models\Alumno::where('userId', $user->id)->first();
        if (!$alumno) return back()->with('error', 'Perfil de alumno no encontrado');

        $data = $request->validate([
            'sensacion' => 'nullable|string',
            'dificultad' => 'required|integer|min:1|max:10',
            'molestias' => 'nullable|string',
            'comentarios' => 'nullable|string',
        ]);

        $data['id'] = 'er' . bin2hex(random_bytes(10));
        $data['entrenamientoId'] = $id;
        $data['alumnoId'] = $alumno->id;

        \App\Models\EntrenamientoResultado::updateOrCreate(
            ['entrenamientoId' => $id, 'alumnoId' => $alumno->id],
            $data
        );

        return back()->with('success', '¡Entrenamiento completado! Gracias por tu feedback.');
    }
}
