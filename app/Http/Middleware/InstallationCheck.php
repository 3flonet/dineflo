<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallationCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lockFile = storage_path('installed.lock');
        $isInstalled = file_exists($lockFile);

        // 🛠️ Resilience: Force non-DB drivers if not installed
        if (!$isInstalled) {
            config([
                'session.driver'   => 'file',
                'cache.default'    => 'file',
                'database.default' => 'mysql',
            ]);
        }

        // If not installed and trying to access anything other than installer routes
        if (!$isInstalled) {
            if (!$request->is('install*') && !$request->is('livewire*') && !$request->is('_debugbar*')) {
                return redirect()->to('/install');
            }
        } 
        
        // If already installed and trying to access installer routes
        if (file_exists($lockFile) && $request->is('install*')) {
            return redirect('/');
        }

        return $next($request);
    }
}
