<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['userId', 'key', 'value'];

    public static function get(string $key, $default = null, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        $setting = self::where('userId', $userId)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        return self::updateOrCreate(['userId' => $userId, 'key' => $key], ['value' => $value]);
    }
}
