<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupLicenseHubVariables extends Command
{
    protected $signature = 'license:setup {secret?} {url?}';
    protected $description = 'Quickly setup LicenseHub development variables in .env';

    public function handle()
    {
        $secret = $this->argument('secret') ?: 'test-secret-dineflo-pos-12345';
        $url = $this->argument('url') ?: 'https://license.3flo.net/api/v1';
        $slug = 'dineflo-pos';

        $this->updateEnv([
            'LICENSEHUB_API_URL' => $url,
            'LICENSEHUB_PRODUCT_SLUG' => $slug,
            'LICENSEHUB_PRODUCT_SECRET' => $secret,
        ]);

        $this->info("✓ LicenseHub Environment Variables Updated:");
        $this->line("URL: <fg=cyan>$url</>");
        $this->line("Slug: <fg=cyan>$slug</>");
        $this->line("Secret: <fg=yellow>$secret</>");
        $appUrl = rtrim(config('app.url'), '/');
        $this->info("\nYou can now visit $appUrl/install/license to test activation.");
    }

    protected function updateEnv(array $data)
    {
        $path = base_path('.env');
        if (!file_exists($path)) return;

        $env = file_get_contents($path);
        foreach ($data as $key => $value) {
            if (str_contains($env, "\n" . $key . '=')) {
                $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
            } else {
                $env .= "\n{$key}={$value}";
            }
        }
        file_put_contents($path, $env);
    }
}
