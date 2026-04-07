<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Filament\Resources\LicenseResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class ViewLicense extends Page
{
    protected static string $resource = LicenseResource::class;

    protected static string $view = 'filament.resources.license-resource.pages.view-license';

    protected static ?string $title = 'License Management';

    public function getHeading(): string
    {
        return 'License Management';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('ping')
                ->label('Ping License Server')
                ->color('info')
                ->icon('heroicon-m-arrow-path')
                ->action('pingLiceseHub'),

            Actions\Action::make('reset')
                ->label('Reset License')
                ->color('danger')
                ->icon('heroicon-m-exclamation-triangle')
                ->requiresConfirmation()
                ->modalHeading('Reset License?')
                ->modalDescription('This will remove the current license and enable license verification step again.')
                ->modalSubmitActionLabel('Yes, reset license')
                ->action('resetLicense')
                ->visible(env('LICENSE_KEY') !== null),
        ];
    }

    public function pingLiceseHub()
    {
        try {
            $licenseKey = env('LICENSE_KEY');
            $licenseDomain = env('LICENSE_DOMAIN');
            $licenseHubUrl = env('LICENSEHUB_API_URL', 'http://licensehub.test');
            $productSecret = env('LICENSEHUB_PRODUCT_SECRET');

            if (!$licenseKey || !$licenseDomain || !$productSecret) {
                Notification::make()
                    ->title('License Not Configured')
                    ->body('Please configure license first.')
                    ->danger()
                    ->send();
                return;
            }

            $response = Http::withHeaders([
                'X-Product-Secret' => $productSecret,
                'Accept' => 'application/json',
            ])->post("{$licenseHubUrl}/api/v1/licenses/ping", [
                'license_key' => $licenseKey,
                'domain' => $licenseDomain,
            ]);

            if ($response->failed()) {
                Notification::make()
                    ->title('Ping Failed')
                    ->body('Failed to ping LicenseHub API: ' . $response->status())
                    ->danger()
                    ->send();
                return;
            }

            $data = $response->json();

            if ($data['status'] !== 'success') {
                Notification::make()
                    ->title('Ping Failed')
                    ->body($data['message'] ?? 'Unknown error')
                    ->danger()
                    ->send();
                return;
            }

            // Update .env file
            $this->updateEnvFile([
                'LICENSE_STATUS' => $data['data']['license']['status'] ?? env('LICENSE_STATUS'),
                'LICENSE_GRACE_UNTIL' => $data['data']['license']['grace_period_until'] ?? '',
                'LICENSE_LAST_PING_AT' => now()->toIso8601String(),
            ]);

            Notification::make()
                ->title('License Ping Successful')
                ->body('License status: ' . $data['data']['license']['status'])
                ->success()
                ->send();

            // Reload the page
            $this->redirect(request()->url());
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Error during license ping: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetLicense()
    {
        try {
            // Reset license environment variables
            $this->updateEnvFile([
                'LICENSE_KEY' => '',
                'LICENSE_STATUS' => '',
                'LICENSE_DOMAIN' => '',
                'LICENSE_CUSTOMER_NAME' => '',
                'LICENSE_CUSTOMER_EMAIL' => '',
                'LICENSE_LAST_PING_AT' => '',
                'LICENSE_GRACE_UNTIL' => '',
            ]);

            Notification::make()
                ->title('License Reset')
                ->body('License has been reset. Please configure a new license.')
                ->success()
                ->send();

            // Redirect to installer
            $this->redirect('/install/license');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Error resetting license: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function updateEnvFile(array $updates): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return;
        }

        $content = file_get_contents($envPath);

        foreach ($updates as $key => $value) {
            $value = $value ?? '';
            $pattern = "/^{$key}=.*/m";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);

        if (file_exists(bootstrap_path('cache/config.php'))) {
            unlink(bootstrap_path('cache/config.php'));
        }
    }
}
