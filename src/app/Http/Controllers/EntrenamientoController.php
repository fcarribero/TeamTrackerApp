<?php
namespace App\Http\Controllers;
use App\Services\EntrenamientoService;
use App\Services\AlumnoService;
use App\Services\GrupoService;
use App\Services\PlantillaService;
use App\Services\AnuncioService;
use App\Services\ChatGPTService;
use Illuminate\Http\Request;

class EntrenamientoController extends Controller {
    protected $service;
    protected $alumnoService;
    protected $grupoService;
    protected $plantillaService;
    protected $anuncioService;
    protected $chatGPTService;

    public function __construct(
        EntrenamientoService $service,
        AlumnoService $alumnoService,
        GrupoService $grupoService,
        PlantillaService $plantillaService,
        AnuncioService $anuncioService,
        ChatGPTService $chatGPTService
    ) {
        $this->service = $service;
        $this->alumnoService = $alumnoService;
        $this->grupoService = $grupoService;
        $this->plantillaService = $plantillaService;
        $this->anuncioService = $anuncioService;
        $this->chatGPTService = $chatGPTService;
    }

    public function index() {
        $profesorId = auth()->id();
        $entrenamientos = \App\Models\Entrenamiento::where('profesorId', $profesorId)
            ->with(['alumnos', 'grupos.alumnos', 'plantilla'])
            ->withCount('resultados')
            ->orderBy('fecha', 'desc')
            ->get();
        $profesor = auth()->user();
        return view('profesor.entrenamientos.index', compact('entrenamientos', 'profesor'));
    }

    public function show($id) {
        $entrenamiento = \App\Models\Entrenamiento::with(['resultados.alumno', 'alumnos', 'grupos'])->findOrFail($id);
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
            'alumnos.*' => 'exists:users,id',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id',
            'plantillaId' => 'nullable|string|exists:plantillas_entrenamiento,id',
            'contenidoPersonalizado' => 'nullable|array',
            'contenidoPersonalizado.calentamiento' => 'nullable|string',
            'contenidoPersonalizado.trabajo_principal' => 'nullable|string',
            'contenidoPersonalizado.enfriamiento' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

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
                ($data['contenidoPersonalizado']['calentamiento'] ?? '') === "20' CCL" &&
                empty($data['contenidoPersonalizado']['trabajo_principal'] ?? '') &&
                ($data['contenidoPersonalizado']['enfriamiento'] ?? '') === "10' CCL";

            if (empty($data['contenidoPersonalizado']) ||
                (is_array($data['contenidoPersonalizado']) &&
                 empty($data['contenidoPersonalizado']['calentamiento']) &&
                 empty($data['contenidoPersonalizado']['trabajo_principal']) &&
                 empty($data['contenidoPersonalizado']['enfriamiento'])) ||
                $esPorDefecto) {
                $data['contenidoPersonalizado'] = $plantilla->contenido;
            }
        }

        $data['profesorId'] = auth()->id();
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
            'alumnos.*' => 'exists:users,id',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id',
            'plantillaId' => 'nullable|string|exists:plantillas_entrenamiento,id',
            'contenidoPersonalizado' => 'nullable|array',
            'contenidoPersonalizado.calentamiento' => 'nullable|string',
            'contenidoPersonalizado.trabajo_principal' => 'nullable|string',
            'contenidoPersonalizado.enfriamiento' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'distanciaTotal' => 'nullable|numeric|min:0',
            'tiempoTotal' => 'nullable|integer|min:0',
        ]);

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

    public function estimarMetricas(Request $request) {
        $data = $request->validate([
            'contenidoPersonalizado' => 'required|array',
            'contenidoPersonalizado.calentamiento' => 'nullable|string',
            'contenidoPersonalizado.trabajo_principal' => 'nullable|string',
            'contenidoPersonalizado.enfriamiento' => 'nullable|string',
        ]);

        $metrics = $this->chatGPTService->estimateTrainingMetrics($data['contenidoPersonalizado']);

        if (!$metrics) {
            return response()->json(['error' => 'No se pudo obtener la estimación de la IA'], 500);
        }

        return response()->json($metrics);
    }

    public function destroy($id) {
        $this->service->delete($id);
        return redirect()->route('entrenamientos.index')->with('success', 'Entrenamiento eliminado correctamente');
    }

    public function indexAlumno() {
        $alumno = auth()->user();
        if (!$alumno->isAlumno()) return redirect('/')->with('error', 'Perfil de alumno no encontrado');

        $profesorId = session('active_profesor_id');

        if (!$profesorId) {
            $profesorIds = $alumno->grupos()->distinct()->pluck('profesorId');
            if ($profesorIds->count() > 1) {
                return redirect()->route('grupos.seleccionar');
            } elseif ($profesorIds->count() === 1) {
                $profesorId = $profesorIds->first();
                session(['active_profesor_id' => $profesorId]);
            } else {
                return view('alumno.entrenamientos.index', [
                    'entrenamientos' => collect(),
                    'anuncioActivo' => null,
                    'profesor' => null
                ]);
            }
        }

        $anuncioActivo = $this->anuncioService->getAnuncioActivo($profesorId);
        $entrenamientos = $this->service->getForAlumno($alumno->id, $profesorId);

        $profesor = \App\Models\User::find($profesorId);

        return view('alumno.entrenamientos.index', compact('entrenamientos', 'anuncioActivo', 'profesor'));
    }

    public function completarAlumno(Request $request, $id) {
        $alumno = auth()->user();
        if (!$alumno->isAlumno()) return back()->with('error', 'Perfil de alumno no encontrado');

        $data = $request->validate([
            'sensacion' => 'nullable|string',
            'dificultad' => 'required|integer|min:1|max:10',
            'molestias' => 'nullable|string',
            'comentarios' => 'nullable|string',
            'fecha_realizado' => 'required|date',
        ]);

        $data['entrenamientoId'] = $id;
        $data['alumnoId'] = $alumno->id;

        // Buscar si ya existe para mantener el ID original
        $existente = \App\Models\EntrenamientoResultado::where('entrenamientoId', $id)
            ->where('alumnoId', $alumno->id)
            ->first();

        if (!$existente) {
            $data['id'] = 'er' . bin2hex(random_bytes(10));
        }

        $resultado = \App\Models\EntrenamientoResultado::updateOrCreate(
            ['entrenamientoId' => $id, 'alumnoId' => $alumno->id],
            $data
        );

        // Si el usuario tiene ubicación, intentamos obtener el clima para esa fecha/hora
        if ($alumno->latitud && $alumno->longitud) {
            $weatherService = app(\App\Services\WeatherService::class);
            $weatherService->getWeather((float)$alumno->latitud, (float)$alumno->longitud, \Carbon\Carbon::parse($data['fecha_realizado']));
        }

        return back()->with('success', '¡Entrenamiento completado! Gracias por tu feedback.');
    }
}
