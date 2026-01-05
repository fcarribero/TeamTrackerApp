<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarminAccount extends Model
{
    use HasFactory;

    protected $table = 'garmin_accounts';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'alumno_id',
        'garmin_user_id',
        'access_token',
        'token_secret',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function alumno()
    {
        return $this->belongsTo(User::class, 'alumno_id');
    }
}
