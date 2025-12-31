<?php
namespace App\Repositories;
use App\Models\Pago;
class PagoRepository extends BaseRepository {
    public function __construct(Pago $model) { parent::__construct($model); }
    public function getAllWithAlumno() { return $this->model->with('alumno')->get(); }
    public function getForAlumno(string $alumnoId, string $profesorId = null) {
        $query = $this->model->where('alumnoId', $alumnoId);
        if ($profesorId) {
            $query->where('profesorId', $profesorId);
        }
        return $query->with('profesor')->orderBy('mesCorrespondiente', 'desc')->get();
    }
}
