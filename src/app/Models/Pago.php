<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'alumnoId',
        'profesorId',
        'monto',
        'fechaPago',
        'mesCorrespondiente',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fechaPago' => 'datetime',
        'monto' => 'double',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumnoId');
    }
}
