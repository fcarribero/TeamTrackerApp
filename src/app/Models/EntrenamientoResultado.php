<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrenamientoResultado extends Model
{
    use HasFactory;

    protected $table = 'entrenamiento_resultados';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'entrenamientoId',
        'alumnoId',
        'sensacion',
        'dificultad',
        'molestias',
        'comentarios',
    ];

    public function entrenamiento()
    {
        return $this->belongsTo(Entrenamiento::class, 'entrenamientoId');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumnoId');
    }
}
