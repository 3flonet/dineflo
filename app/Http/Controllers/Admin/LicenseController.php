<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LicenseController extends Controller
{
    /**
     * Show the license status page (The "Locked Screen" info)
     */
    public function index()
    {
        $service = app(LicenseService::class);
        $status = $service->check();

        return view('admin.license.index', [
            'status' => $status,
            'licenseKey' => env('LICENSE_KEY', 'N/A'),
            'domain' => request()->getHost(),
            'hubUrl' => env('LICENSE_HUB_URL', 'https://license.3flo.net'),
        ]);
    }

    /**
     * Force sync with the Hub (Manually Refresh Status)
     */
    public function sync()
    {
        // 1. Clear local cache and locks
        Cache::forget('license_status');
        Cache::forget('license_check_lock');

        // 2. Perform fresh ping check
        $service = app(LicenseService::class);
        $newStatus = $service->check();

        if (in_array($newStatus, ['active', 'grace_period'])) {
            return redirect('/admin')->with('success', 'Lisensi Berhasil Diaktifkan! Selamat Jualan.');
        }

        return back()->with('error', 'Status Lisensi Masih: ' . strtoupper($newStatus) . '. Silakan periksa di portal atau hubungi admin.');
    }

    /**
     * Activate a completely NEW License Key
     */
    public function activate(Request $request)
    {
        $request->validate([
            'new_license_key' => 'required|string|min:10',
        ]);

        $service = app(LicenseService::class);
        $result = $service->verify($request->new_license_key);

        if ($result['success']) {
            // Update .env permenently
            \App\Helpers\EnvHelper::set('LICENSE_KEY', $request->new_license_key);
            
            // Clear cache and redirect
            Cache::forget('license_status');
            Cache::forget('license_check_lock');

            return redirect('/admin')->with('success', 'Kunci Lisensi Baru Berhasil Diaktifkan! Selamat Jualan.');
        }

        return back()->with('error', 'Gagal Aktivasi: ' . ($result['message'] ?? 'Kunci tidak valid.'));
    }
}
