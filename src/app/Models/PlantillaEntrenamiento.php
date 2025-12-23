<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaEntrenamiento extends Model
{
    use HasFactory;

    protected $table = 'plantillas_entrenamiento';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'contenido',
        'observaciones',
    ];

    protected $casts = [
        'contenido' => 'array',
    ];

    public function entrenamientos()
    {
        return $this->hasMany(Entrenamiento::class, 'plantillaId');
    }
}
