<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemHealth extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'System Health';
    protected static ?string $title = 'System Health & Monitoring';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('optimizeClear')
                ->label('Clear System Cache & Prune Storage')
                ->tooltip('Menghapus cache Laravel dan membersihkan file storage/gambar yang sudah tidak terpakai.')
                ->action(function () {
                    Artisan::call('optimize:clear');
                    Artisan::call('system:prune-storage', ['--force' => true]);

                    Notification::make()
                        ->title('System cache & storage cleaned successfully')
                        ->success()
                        ->send();
                })
                ->color('warning')
                ->requiresConfirmation(),
        ];
    }

    protected static string $view = 'filament.admin.pages.system-health';

    public array $healthStatus = [];

    public function mount()
    {
        $this->healthStatus = [
            'database'   => $this->checkDatabase(),
            'cache'      => $this->checkCache(),
            'storage'    => $this->checkStorage(),
            'disk_space' => $this->checkDiskSpace(),
        ];
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
            Cache::put('health_check_admin', true, 10);
            $val = Cache::get('health_check_admin');
            Cache::forget('health_check_admin');
            return ['healthy' => $val === true, 'message' => $val === true ? 'Working' : 'Cache fail'];
        } catch (\Exception $e) {
            return ['healthy' => false, 'message' => $e->getMessage()];
        }
    }

    protected function checkStorage(): array
    {
        try {
            $filename = 'health_check_admin.txt';
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
        try {
            $free = disk_free_space(base_path());
            $total = disk_total_space(base_path());
            
            if ($total === 0) {
                return ['healthy' => false, 'message' => 'Cannot determine disk space'];
            }
            
            $freePercentage = ($free / $total) * 100;
            return [
                'healthy' => $freePercentage > 10,
                'message' => number_format($freePercentage, 2) . '% free (' . number_format($free / 1024 / 1024 / 1024, 2) . ' GB)',
            ];
        } catch (\Exception $e) {
            return ['healthy' => false, 'message' => $e->getMessage()];
        }
    }
}
