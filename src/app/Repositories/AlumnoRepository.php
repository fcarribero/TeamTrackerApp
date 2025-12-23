<?php

namespace App\Repositories;

use App\Models\Alumno;

class AlumnoRepository extends BaseRepository
{
    public function __construct(Alumno $model)
    {
        parent::__construct($model);
    }

    public function getAllWithRelations()
    {
        return $this->model->with(['user', 'grupos'])->get();
    }

    public function getByIdWithDetails(string $id)
    {
        return $this->model->with([
            'pagos' => fn($q) => $q->orderBy('fechaPago', 'desc'),
            'entrenamientos' => fn($q) => $q->orderBy('fecha', 'desc'),
            'grupos'
        ])->find($id);
    }
}
