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
        'name',
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
        ];
    }

    public function alumno()
    {
        return $this->hasOne(Alumno::class, 'userId');
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
        return $this->belongsToMany(Alumno::class, 'profesor_alumno', 'profesor_id', 'alumno_id')->withTimestamps();
    }
}
