<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SubscriptionCheckStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check active subscriptions for expiry and send notifications';

    public function handle()
    {
        $this->info('Starting subscription check...');

        // 1. Mark Expired Subscriptions
        $expiredSubs = \App\Models\Subscription::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredSubs as $sub) {
            $sub->update(['status' => 'expired']);
            
            // Send Email
            try {
                \Mail::to($sub->user->email)->send(new \App\Mail\SubscriptionStatusMail($sub->user, $sub, 'expired'));
                $this->info("Expired email sent to: {$sub->user->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$sub->user->email}: " . $e->getMessage());
            }
        }
        $this->info("Processed " . $expiredSubs->count() . " expired subscriptions.");

        // 2. Send Warnings
        $threshold = app(\App\Settings\GeneralSettings::class)->subscription_expiry_warning_days;
        
        // Check subscriptions expiring between threshold-1 and threshold days from now
        $warningSubs = \App\Models\Subscription::where('status', 'active')
            ->whereBetween('expires_at', [now()->addDays($threshold - 1), now()->addDays($threshold)])
            ->get();

        foreach ($warningSubs as $sub) {
            try {
                \Mail::to($sub->user->email)->send(new \App\Mail\SubscriptionStatusMail($sub->user, $sub, 'warning'));
                $this->info("Warning (H-{$threshold}) sent to: {$sub->user->email}");
            } catch (\Exception $e) {
                // Log error
            }
        }
        
        $this->info('Check complete.');
    }
}
