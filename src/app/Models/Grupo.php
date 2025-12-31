<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'profesorId',
        'nombre',
        'descripcion',
        'color',
    ];

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesorId');
    }

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'grupos_alumnos', 'grupoId', 'alumnoId');
    }

    public function entrenamientos()
    {
        return $this->belongsToMany(Entrenamiento::class, 'entrenamientos_grupos', 'grupoId', 'entrenamientoId');
    }
}
