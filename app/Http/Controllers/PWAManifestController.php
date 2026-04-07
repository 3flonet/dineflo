<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Storage;

class PWAManifestController extends Controller
{
    public function manifest(GeneralSettings $settings)
    {
        $icon192 = $settings->pwa_icon_192 
            ? Storage::url($settings->pwa_icon_192) 
            : ($settings->site_logo ? Storage::url($settings->site_logo) : asset('logo.png'));

        $icon512 = $settings->pwa_icon_512 
            ? Storage::url($settings->pwa_icon_512) 
            : ($settings->site_logo ? Storage::url($settings->site_logo) : asset('logo.png'));

        $manifest = [
            "name" => $settings->site_name ?: (config('app.name', 'Dineflo') . " - Restaurant OS"),
            "short_name" => $settings->site_name ?: config('app.name', 'Dineflo'),
            "start_url" => "/",
            "background_color" => "#ffffff",
            "description" => $settings->site_description ?: "Smart Restaurant Operating System",
            "display" => "standalone",
            "theme_color" => "#F59E0B",
            "icons" => [
                [
                    "src" => $icon192,
                    "sizes" => "192x192",
                    "type" => "image/png",
                    "purpose" => "any maskable"
                ],
                [
                    "src" => $icon512,
                    "sizes" => "512x512",
                    "type" => "image/png",
                    "purpose" => "any maskable"
                ]
            ]
        ];

        return response()->json($manifest)->header('Content-Type', 'application/manifest+json');
    }
}
