<?php
namespace App\Repositories;
use App\Models\Entrenamiento;
class EntrenamientoRepository extends BaseRepository {
    public function __construct(Entrenamiento $model) { parent::__construct($model); }
    public function getAllWithRelations() { return $this->model->with(['alumnos', 'grupos', 'plantilla'])->withCount('resultados')->orderBy('fecha', 'desc')->get(); }

    public function getForAlumno(string $alumnoId, string $profesorId = null)
    {
        $query = $this->model->where(function($query) use ($alumnoId) {
            $query->whereHas('alumnos', function($q) use ($alumnoId) {
                $q->where('alumnoId', $alumnoId);
            })->orWhereHas('grupos', function($q) use ($alumnoId) {
                $q->whereHas('alumnos', function($sq) use ($alumnoId) {
                    $sq->where('alumnoId', $alumnoId);
                });
            });
        });

        if ($profesorId) {
            $query->where('profesorId', $profesorId);
        }

        return $query->with(['alumnos', 'grupos', 'plantilla', 'resultados' => function($q) use ($alumnoId) {
            $q->where('alumnoId', $alumnoId);
        }])
          ->orderBy('fecha', 'desc')
          ->get();
    }
    public function getWithResultados(string $id)
    {
        return $this->model->with(['resultados.alumno', 'alumnos', 'grupos'])->find($id);
    }
}
