<?php
namespace App\Repositories;
use App\Models\ObjetivoCompetencia;
class ObjetivoRepository extends BaseRepository {
    public function __construct(ObjetivoCompetencia $model) { parent::__construct($model); }
    public function getAllWithRelations() { return $this->model->with(['alumno', 'competenciaPreestablecida'])->get(); }
}
