<?php
namespace App\Repositories;
use App\Models\Pago;
class PagoRepository extends BaseRepository {
    public function __construct(Pago $model) { parent::__construct($model); }
    public function getAllWithAlumno() { return $this->model->with('alumno')->get(); }
}
