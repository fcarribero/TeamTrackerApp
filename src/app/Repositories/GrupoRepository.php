<?php
namespace App\Repositories;
use App\Models\Grupo;
class GrupoRepository extends BaseRepository {
    public function __construct(Grupo $model) { parent::__construct($model); }
    public function getAllWithAlumnos() { return $this->model->with('alumnos')->get(); }
}
