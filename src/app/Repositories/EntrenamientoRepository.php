<?php
namespace App\Repositories;
use App\Models\Entrenamiento;
class EntrenamientoRepository extends BaseRepository {
    public function __construct(Entrenamiento $model) { parent::__construct($model); }
    public function getAllWithRelations() { return $this->model->with(['alumnos', 'grupos', 'plantilla'])->get(); }

    public function getForAlumno(string $alumnoId)
    {
        return $this->model->whereHas('alumnos', function($q) use ($alumnoId) {
            $q->where('alumnoId', $alumnoId);
        })->orWhereHas('grupos', function($q) use ($alumnoId) {
            $q->whereHas('alumnos', function($sq) use ($alumnoId) {
                $sq->where('alumnoId', $alumnoId);
            });
        })->with(['alumnos', 'grupos', 'plantilla'])
          ->orderBy('fecha', 'desc')
          ->get();
    }
}
