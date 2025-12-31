<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherHistory extends Model
{
    use HasFactory;

    protected $table = 'weather_history';

    protected $fillable = [
        'latitud',
        'longitud',
        'fecha_hora',
        'temperatura',
        'humedad',
        'cielo',
        'descripcion',
        'icono',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'latitud' => 'float',
        'longitud' => 'float',
        'temperatura' => 'float',
        'humedad' => 'float',
    ];
}
