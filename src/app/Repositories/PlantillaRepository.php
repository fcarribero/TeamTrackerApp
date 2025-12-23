<?php
namespace App\Repositories;
use App\Models\PlantillaEntrenamiento;
class PlantillaRepository extends BaseRepository {
    public function __construct(PlantillaEntrenamiento $model) { parent::__construct($model); }
}
