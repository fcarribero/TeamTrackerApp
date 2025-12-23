<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjetivoCompetencia extends Model
{
    use HasFactory;

    protected $table = 'objetivos_competencias';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'alumnoId',
        'competenciaPreestablecidaId',
        'nombre',
        'tipo',
        'distancia',
        'fecha',
        'tiempoObjetivo',
        'numeroDorsal',
        'resultado',
        'notasProfesor',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumnoId');
    }

    public function competenciaPreestablecida()
    {
        return $this->belongsTo(CompetenciaPreestablecida::class, 'competenciaPreestablecidaId');
    }
}
