<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'google_id',
        'email',
        'password',
        'nombre',
        'apellido',
        'dni',
        'fechaNacimiento',
        'sexo',
        'obra_social',
        'numero_socio',
        'certificado_medico',
        'vencimiento_certificado',
        'notas',
        'rol',
        'email_verified_at',
        'image',
        'ciudad',
        'latitud',
        'longitud',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fechaNacimiento' => 'datetime',
            'vencimiento_certificado' => 'date',
        ];
    }

    public function getNameAttribute()
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }

    public function isAlumno()
    {
        return $this->rol === 'alumno';
    }

    public function isProfesor()
    {
        return $this->rol === 'profesor';
    }

    public function isAdmin()
    {
        return $this->rol === 'admin';
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

    public function competencias()
    {
        return $this->hasMany(Competencia::class, 'alumno_id');
    }

    public function resultadosEntrenamientos()
    {
        return $this->hasMany(EntrenamientoResultado::class, 'alumnoId');
    }

    public function garminAccount()
    {
        return $this->hasOne(GarminAccount::class, 'alumno_id');
    }

    public function garminActivities()
    {
        return $this->hasMany(GarminActivity::class, 'alumno_id');
    }

    public function profesores()
    {
        return $this->belongsToMany(User::class, 'profesor_alumno', 'alumno_id', 'profesor_id')->withTimestamps();
    }

    public function gruposManaged()
    {
        return $this->hasMany(Grupo::class, 'profesorId');
    }

    public function settings()
    {
        return $this->hasMany(Setting::class, 'userId');
    }

    public function alumnos()
    {
        return $this->belongsToMany(User::class, 'profesor_alumno', 'profesor_id', 'alumno_id')->withTimestamps();
    }
}
