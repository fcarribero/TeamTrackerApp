<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenamiento extends Model
{
    use HasFactory;

    protected $table = 'entrenamientos';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'profesorId',
        'fecha',
        'titulo',
        'plantillaId',
        'plantillaNombre',
        'ejercicios',
        'contenidoPersonalizado',
        'observaciones',
        'distanciaTotal',
        'tiempoTotal',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'contenidoPersonalizado' => 'array',
        'ejercicios' => 'array',
    ];

    public function alumnos()
    {
        return $this->belongsToMany(User::class, 'entrenamientos_alumnos', 'entrenamientoId', 'alumnoId');
    }

    public function plantilla()
    {
        return $this->belongsTo(PlantillaEntrenamiento::class, 'plantillaId');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'entrenamientos_grupos', 'entrenamientoId', 'grupoId');
    }

    public function resultados()
    {
        return $this->hasMany(EntrenamientoResultado::class, 'entrenamientoId');
    }
}
