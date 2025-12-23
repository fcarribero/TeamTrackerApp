<?php
namespace App\Repositories;
use App\Models\CompetenciaPreestablecida;
class CompetenciaRepository extends BaseRepository {
    public function __construct(CompetenciaPreestablecida $model) { parent::__construct($model); }
}
