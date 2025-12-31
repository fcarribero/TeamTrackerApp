<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitacion extends Model
{
    //

    protected $table = 'invitaciones';

    protected $fillable = [
        'email',
        'profesorId',
        'grupoId',
        'token',
        'status',
        'accepted_at',
    ];

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesorId');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupoId');
    }
}
