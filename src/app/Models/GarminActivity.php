<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarminActivity extends Model
{
    use HasFactory;

    protected $table = 'garmin_activities';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'alumno_id',
        'garmin_activity_id',
        'name',
        'activity_type',
        'start_time',
        'distance',
        'duration',
        'average_speed',
        'max_speed',
        'calories',
        'average_hr',
        'max_hr',
        'raw_data',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'raw_data' => 'array',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }
}
