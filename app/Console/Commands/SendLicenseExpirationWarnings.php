<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\LicenseExpirationWarning;

class SendLicenseExpirationWarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:send-warnings {--days=7 : Days remaining before expiration to trigger warning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check license expiration dates and send warning emails to customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $licenseKey = env('LICENSE_KEY');
        $licenseStatus = env('LICENSE_STATUS', 'inactive');
        $expiresAt = env('LICENSE_EXPIRES_AT');
        $gracePeriodUntil = env('LICENSE_GRACE_UNTIL');
        $customerEmail = env('LICENSE_CUSTOMER_EMAIL');
        $customerName = env('LICENSE_CUSTOMER_NAME');

        // Skip if license not configured
        if (!$licenseKey || !$customerEmail) {
            $this->warn('License not properly configured. Skipping warning email.');
            Log::warning('License warning skipped: missing license or customer email');
            return self::FAILURE;
        }

        // Skip if license is not expiring soon
        if (!$expiresAt) {
            $this->info('No expiration date configured. No warning sent.');
            return self::SUCCESS;
        }

        $expirationDate = Carbon::parse($expiresAt);
        $daysRemaining = Carbon::now()->diffInDays($expirationDate, false);
        $warningDays = (int) $this->option('days');

        // Only send warning if expiration is within warning period
        if ($daysRemaining > $warningDays || $daysRemaining < 0) {
            $this->info("License expires in {$daysRemaining} days. Warning threshold is {$warningDays} days.");
            return self::SUCCESS;
        }

        try {
            $this->info("Sending expiration warning for license: {$licenseKey}");

            Mail::send(new LicenseExpirationWarning(
                customerName: $customerName,
                customerEmail: $customerEmail,
                licenseKey: $licenseKey,
                expiresAt: $expiresAt,
                gracePeriodUntil: $gracePeriodUntil,
                daysRemaining: max(0, $daysRemaining),
            ));

            Log::info('License expiration warning sent', [
                'license_key' => substr($licenseKey, 0, 10) . '...',
                'customer_email' => $customerEmail,
                'days_remaining' => $daysRemaining,
            ]);

            $this->info("✓ Expiration warning email sent to {$customerEmail}");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to send expiration warning: {$e->getMessage()}");
            Log::error('License warning email failed', [
                'error' => $e->getMessage(),
                'customer_email' => $customerEmail,
            ]);
            return self::FAILURE;
        }
    }
}
