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
        'fecha_realizado',
        'sensacion',
        'dificultad',
        'molestias',
        'comentarios',
    ];

    protected $casts = [
        'fecha_realizado' => 'datetime',
    ];

    public function entrenamiento()
    {
        return $this->belongsTo(Entrenamiento::class, 'entrenamientoId');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumnoId');
    }

    public function weather()
    {
        if (!$this->fecha_realizado) return null;

        $user = $this->alumno->user;
        if (!$user || !$user->latitud || !$user->longitud) return null;

        $hour = $this->fecha_realizado->copy()->startOfHour();
        return WeatherHistory::where('latitud', (float)$user->latitud)
            ->where('longitud', (float)$user->longitud)
            ->where('fecha_hora', $hour)
            ->first();
    }
}
