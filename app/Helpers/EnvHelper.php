<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class EnvHelper
{
    /**
     * Update or set a value in the .env file
     */
    public static function set(string $key, string $value): bool
    {
        $path = base_path('.env');

        if (!File::exists($path)) {
            return false;
        }

        $content = File::get($path);
        $key = strtoupper($key);
        
        // Check if key exists (supports optional quotes and different line endings)
        if (preg_match("/^{$key}=/m", $content)) {
            // Update existing key - ensuring we replace the full line correctly
            $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
        } else {
            // Add new key ensuring a clean newline
            $content = rtrim($content) . "\n{$key}=\"{$value}\"\n";
        }

        try {
            File::put($path, $content);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update multiple keys at once
     */
    public static function setMany(array $data): bool
    {
        $success = true;
        foreach ($data as $key => $value) {
            if (!self::set($key, (string) $value)) {
                $success = false;
            }
        }
        return $success;
    }
}
