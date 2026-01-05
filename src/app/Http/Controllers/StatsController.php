<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Entrenamiento;
use App\Models\Pago;
use App\Services\EntrenamientoService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatsController extends Controller
{
    protected $entrenamientoService;

    public function __construct(EntrenamientoService $entrenamientoService)
    {
        $this->entrenamientoService = $entrenamientoService;
    }

    public function profesorDashboard()
    {
        $profesorId = auth()->id();
        $totalAlumnos = User::where('rol', 'alumno')->whereHas('grupos', function($q) use ($profesorId) {
            $q->where('profesorId', $profesorId);
        })->count();
        $pagosPendientes = Pago::where('profesorId', $profesorId)->where('estado', 'pendiente')->count();

        $mesActual = Carbon::now()->format('Y-m');
        $ingresosMesActual = Pago::where('profesorId', $profesorId)->where('mesCorrespondiente', $mesActual)
            ->where('estado', 'pagado')
            ->sum('monto');

        $hoy = Carbon::today();
        $enSieteDias = Carbon::today()->addDays(7);

        $proximosEntrenamientos = Entrenamiento::where('profesorId', $profesorId)
            ->with(['alumnos', 'grupos'])
            ->whereBetween('fecha', [$hoy, $enSieteDias])
            ->orderBy('fecha', 'asc')
            ->take(5)
            ->get();

        $ultimosAlumnos = User::where('rol', 'alumno')->whereHas('grupos', function($q) use ($profesorId) {
            $q->where('profesorId', $profesorId);
        })->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $profesor = auth()->user();

        return view('profesor.dashboard', compact(
            'totalAlumnos',
            'pagosPendientes',
            'ingresosMesActual',
            'proximosEntrenamientos',
            'ultimosAlumnos',
            'profesor'
        ));
    }

    public function alumnoDashboard()
    {
        $alumno = auth()->user();

        if (!$alumno->isAlumno()) {
            return redirect('/')->with('error', 'Perfil de alumno no encontrado');
        }

        $hoy = Carbon::today();

        $proximosEntrenamientos = $this->entrenamientoService->getForAlumno($alumno->id)
            ->filter(fn($e) => Carbon::parse($e->fecha)->startOfDay() >= $hoy)
            ->sortBy('fecha')
            ->take(5);

        $pagosMesActual = Pago::where('alumnoId', $alumno->id)
            ->where('mesCorrespondiente', 'LIKE', '%' . Carbon::now()->format('Y-m') . '%')
            ->with('profesor')
            ->orderBy('fechaPago', 'desc')
            ->get();

        $profesor = \App\Models\User::where('rol', 'profesor')->first();

        return view('alumno.dashboard', compact(
            'alumno',
            'proximosEntrenamientos',
            'pagosMesActual',
            'profesor'
        ));
    }

}
