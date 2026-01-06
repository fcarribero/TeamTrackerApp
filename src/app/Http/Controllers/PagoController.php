<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Services\PagoService;
use App\Services\AlumnoService;
use App\Services\GrupoService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagoController extends Controller {
    protected $service;
    protected $alumnoService;
    protected $grupoService;

    public function __construct(PagoService $service, AlumnoService $alumnoService, GrupoService $grupoService) {
        $this->service = $service;
        $this->alumnoService = $alumnoService;
        $this->grupoService = $grupoService;
    }

    public function index(Request $request) {
        $profesorId = auth()->id();
        $search = $request->input('search');

        $query = \App\Models\Pago::where('profesorId', $profesorId)->with('alumno');

        if ($search) {
            $query->whereHas('alumno', function($q) use ($search) {
                $q->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere(\Illuminate\Support\Facades\DB::raw("CONCAT(nombre, ' ', apellido)"), 'like', "%{$search}%");
                });
            });
        }

        $pagos = $query->get();

        $mesActual = Carbon::now()->format('Y-m');

        $stats = [
            'ingresos_mes' => $pagos->where('mesCorrespondiente', $mesActual)->where('estado', 'pagado')->sum('monto'),
            'pendientes_mes' => $pagos->where('mesCorrespondiente', $mesActual)->where('estado', 'pendiente')->count(),
            'vencidos' => $pagos->where('estado', 'vencido')->count() + $pagos->where('estado', 'pendiente')->filter(function($pago) use ($mesActual) {
                return $pago->mesCorrespondiente < $mesActual;
            })->count(),
        ];

        $alumnosVencidos = $pagos->filter(function($pago) use ($mesActual) {
            return $pago->estado === 'vencido' || ($pago->estado === 'pendiente' && $pago->mesCorrespondiente < $mesActual);
        })->map(fn($p) => $p->alumno)->filter()->unique('id');

        return view('profesor.pagos.index', compact('pagos', 'stats', 'alumnosVencidos'));
    }

    public function create() {
        $profesorId = auth()->id();
        $alumnos = User::where('rol', 'alumno')->whereHas('grupos', function($q) use ($profesorId) {
            $q->where('profesorId', $profesorId);
        })->get();
        return view('profesor.pagos.create', compact('alumnos'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'alumnoId' => 'required|string|exists:users,id',
            'monto' => 'required|numeric|min:0',
            'fechaPago' => 'nullable|date',
            'fechaVencimiento' => 'nullable|date',
            'mesCorrespondiente' => 'required|string',
            'estado' => 'required|in:pendiente,pagado,vencido,cancelado',
            'notas' => 'nullable|string',
        ]);

        $data['profesorId'] = auth()->id();

        $this->service->create($data);
        return redirect()->route('pagos.index')->with('success', 'Pago registrado correctamente');
    }

    public function edit($id) {
        $pago = $this->service->find($id);
        if (!$pago) return redirect()->route('pagos.index')->with('error', 'Pago no encontrado');

        $profesorId = auth()->id();
        $alumnos = User::where('rol', 'alumno')->whereHas('grupos', function($q) use ($profesorId) {
            $q->where('profesorId', $profesorId);
        })->get();
        return view('profesor.pagos.edit', compact('pago', 'alumnos'));
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'alumnoId' => 'required|string|exists:users,id',
            'monto' => 'required|numeric|min:0',
            'fechaPago' => 'nullable|date',
            'fechaVencimiento' => 'nullable|date',
            'mesCorrespondiente' => 'required|string',
            'estado' => 'required|in:pendiente,pagado,vencido,cancelado',
            'notas' => 'nullable|string',
        ]);

        $this->service->update($id, $data);
        return redirect()->route('pagos.index')->with('success', 'Pago actualizado correctamente');
    }

    public function destroy($id) {
        $this->service->delete($id);
        return redirect()->route('pagos.index')->with('success', 'Pago eliminado correctamente');
    }

    public function solicitarPagoForm() {
        $profesorId = auth()->id();
        $grupos = \App\Models\Grupo::where('profesorId', $profesorId)->get();
        return view('profesor.pagos.solicitar', compact('grupos'));
    }

    public function solicitarPagoStore(Request $request) {
        $data = $request->validate([
            'grupos' => 'required|array',
            'grupos.*' => 'required|string|exists:grupos,id',
            'monto' => 'required|numeric|min:0',
            'fechaVencimiento' => 'required|date',
            'mesCorrespondiente' => 'required|string',
            'cancelarPrevios' => 'nullable|boolean',
        ]);

        $profesorId = auth()->id();
        $alumnos = \App\Models\User::whereHas('grupos', function($q) use ($data, $profesorId) {
            $q->whereIn('grupoId', $data['grupos'])->where('profesorId', $profesorId);
        })->get();

        if ($alumnos->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron alumnos en los grupos seleccionados');
        }

        $count = 0;
        foreach ($alumnos as $alumno) {
            if ($request->has('cancelarPrevios') && $request->cancelarPrevios) {
                \App\Models\Pago::where('alumnoId', $alumno->id)
                    ->where('mesCorrespondiente', $data['mesCorrespondiente'])
                    ->where('estado', 'pendiente')
                    ->update(['estado' => 'cancelado']);
            }

            $this->service->create([
                'alumnoId' => $alumno->id,
                'profesorId' => $profesorId,
                'monto' => $data['monto'],
                'fechaPago' => now(), // Fecha de creación/solicitud
                'fechaVencimiento' => $data['fechaVencimiento'],
                'mesCorrespondiente' => $data['mesCorrespondiente'],
                'estado' => 'pendiente',
            ]);
            $count++;
        }

        return redirect()->route('pagos.index')->with('success', "Se han creado $count solicitudes de pago correctamente para " . $alumnos->count() . " alumnos.");
    }

    public function indexAlumno() {
        $alumno = auth()->user();
        if (!$alumno->isAlumno()) return redirect('/')->with('error', 'Perfil de alumno no encontrado');

        $pagos = $this->service->getForAlumno($alumno->id);
        return view('alumno.pagos.index', compact('pagos', 'alumno'));
    }
}
