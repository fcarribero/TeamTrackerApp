<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Services\PagoService;
use App\Services\AlumnoService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagoController extends Controller {
    protected $service;
    protected $alumnoService;

    public function __construct(PagoService $service, AlumnoService $alumnoService) {
        $this->service = $service;
        $this->alumnoService = $alumnoService;
    }

    public function index() {
        $profesorId = auth()->id();
        $pagos = \App\Models\Pago::where('profesorId', $profesorId)->with('alumno')->get();

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
            'fechaPago' => 'required|date',
            'mesCorrespondiente' => 'required|string',
            'estado' => 'required|in:pendiente,pagado,vencido',
            'notas' => 'nullable|string',
        ]);

        $data['profesorId'] = auth()->id();

        $this->service->create($data);
        return redirect()->route('pagos.index')->with('success', 'Pago registrado correctamente');
    }

    public function edit($id) {
        $pago = $this->service->find($id);
        if (!$pago) return redirect()->route('pagos.index')->with('error', 'Pago no encontrado');

        $alumnos = $this->alumnoService->getAllAlumnos();
        return view('profesor.pagos.edit', compact('pago', 'alumnos'));
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'alumnoId' => 'required|string|exists:users,id',
            'monto' => 'required|numeric|min:0',
            'fechaPago' => 'required|date',
            'mesCorrespondiente' => 'required|string',
            'estado' => 'required|in:pendiente,pagado,vencido',
            'notas' => 'nullable|string',
        ]);

        $this->service->update($id, $data);
        return redirect()->route('pagos.index')->with('success', 'Pago actualizado correctamente');
    }

    public function destroy($id) {
        $this->service->delete($id);
        return redirect()->route('pagos.index')->with('success', 'Pago eliminado correctamente');
    }

    public function indexAlumno() {
        $alumno = auth()->user();
        if (!$alumno->isAlumno()) return redirect('/')->with('error', 'Perfil de alumno no encontrado');

        $pagos = $this->service->getForAlumno($alumno->id);
        return view('alumno.pagos.index', compact('pagos', 'alumno'));
    }
}
