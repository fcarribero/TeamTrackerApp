<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumnos';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'dni',
        'nombre',
        'apellido',
        'fechaNacimiento',
        'sexo',
        'obra_social',
        'numero_socio',
        'certificado_medico',
        'vencimiento_certificado',
        'notas',
        'userId',
    ];

    protected $casts = [
        'fechaNacimiento' => 'datetime',
        'vencimiento_certificado' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'alumnoId');
    }

    public function entrenamientos()
    {
        return $this->belongsToMany(Entrenamiento::class, 'entrenamientos_alumnos', 'alumnoId', 'entrenamientoId');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupos_alumnos', 'alumnoId', 'grupoId');
    }

    public function objetivos()
    {
        return $this->hasMany(ObjetivoCompetencia::class, 'alumnoId');
    }

    public function resultadosEntrenamientos()
    {
        return $this->hasMany(EntrenamientoResultado::class, 'alumnoId');
    }
}
