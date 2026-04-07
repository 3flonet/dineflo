<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    /**
     * Check application health status.
     */
    public function index()
    {
        $status = [
            'database' => $this->checkDatabase(),
            'cache'    => $this->checkCache(),
            'storage'  => $this->checkStorage(),
            'disk_space' => $this->checkDiskSpace(),
            'timestamp'  => now()->toIso8601String(),
            'environment' => config('app.env'),
        ];

        $isHealthy = !in_array(false, array_column($status, 'healthy'), true);

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'degraded',
            'components' => $status,
        ], $isHealthy ? 200 : 503);
    }

    protected function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['healthy' => true, 'message' => 'Connected'];
        } catch (\Exception $e) {
            return ['healthy' => false, 'message' => $e->getMessage()];
        }
    }

    protected function checkCache(): array
    {
        try {
            Cache::put('health_check', true, 10);
            $val = Cache::get('health_check');
            Cache::forget('health_check');
            return ['healthy' => $val === true, 'message' => $val === true ? 'Working' : 'Cache fail'];
        } catch (\Exception $e) {
            return ['healthy' => false, 'message' => $e->getMessage()];
        }
    }

    protected function checkStorage(): array
    {
        try {
            $filename = 'health_check.txt';
            Storage::disk('public')->put($filename, 'test');
            $exists = Storage::disk('public')->exists($filename);
            Storage::disk('public')->delete($filename);
            return ['healthy' => $exists, 'message' => $exists ? 'Writable' : 'Not writable'];
        } catch (\Exception $e) {
            return ['healthy' => false, 'message' => $e->getMessage()];
        }
    }

    protected function checkDiskSpace(): array
    {
        $free = disk_free_space(base_path());
        $total = disk_total_space(base_path());
        $freePercentage = ($free / $total) * 100;

        return [
            'healthy' => $freePercentage > 10,
            'message' => number_format($freePercentage, 2) . '% free (' . number_format($free / 1024 / 1024 / 1024, 2) . ' GB)',
        ];
    }
}
