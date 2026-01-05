<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    use HasFactory;

    protected $table = 'competencias';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'alumno_id',
        'profesorId',
        'nombre',
        'fecha',
        'ubicación',
        'latitud',
        'longitud',
        'observaciones',
        'plan_carrera',
        'tiempo_objetivo',
        'resultado_obtenido',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function alumno()
    {
        return $this->belongsTo(User::class, 'alumno_id');
    }
}
