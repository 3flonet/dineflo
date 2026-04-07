<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'name', 'payload', 'locked'];

    public static function get($key, $default = null)
    {
        try {
            if (!file_exists(storage_path('installed.lock'))) {
                return $default;
            }
            // Mencari di kolom 'name' (format Spatie)
            $setting = self::where('name', $key)->first();
            if ($setting) {
                $decoded = json_decode($setting->payload, true);
                return is_null($decoded) ? $setting->payload : $decoded;
            }
            return $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
