<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetenciaPreestablecida extends Model
{
    use HasFactory;

    protected $table = 'competencias_preestablecidas';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nombre',
        'tipo',
        'distancia',
        'fecha',
        'linkCompetencia',
        'linkClasificaciones',
        'descripcion',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function objetivos()
    {
        return $this->hasMany(ObjetivoCompetencia::class, 'competenciaPreestablecidaId');
    }
}
