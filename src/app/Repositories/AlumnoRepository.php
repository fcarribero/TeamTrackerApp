<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AlumnoRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function find(string $id): ?Model
    {
        return $this->model->where('rol', 'alumno')->find($id);
    }

    public function getAllWithRelations()
    {
        return $this->model->where('rol', 'alumno')->with(['grupos'])->get();
    }

    public function getByIdWithDetails(string $id)
    {
        return $this->model->where('rol', 'alumno')->with([
            'pagos' => fn($q) => $q->orderBy('fechaPago', 'desc'),
            'entrenamientos' => fn($q) => $q->orderBy('fecha', 'desc'),
            'grupos'
        ])->find($id);
    }
}
