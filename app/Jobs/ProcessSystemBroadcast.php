<?php

namespace App\Jobs;

use App\Models\SystemBroadcast;
use App\Models\User;
use App\Models\SystemBroadcastLog;
use App\Settings\GeneralSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessSystemBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $broadcast;

    /**
     * Create a new job instance.
     */
    public function __construct(SystemBroadcast $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->broadcast->status === 'sent') {
            return;
        }

        $this->broadcast->update(['status' => 'sending']);

        // Find all restaurant owners
        $owners = User::role('restaurant_owner')->get();
        $total = $owners->count();
        $this->broadcast->update(['total_recipients' => $total]);

        if ($total === 0) {
            $this->broadcast->update(['status' => 'sent', 'sent_at' => now()]);
            return;
        }

        // Load platform WA settings once
        $settings     = app(GeneralSettings::class);
        $waActive     = $settings->platform_wa_is_active ?? false;
        $waProvider   = $settings->platform_wa_provider ?? 'fonnte';
        
        // Prepare settings data for jobs to avoid repeated DB/Decryption lookups
        $waSettingsData = [
            'fonnte_key'    => (function() use ($settings) {
                try { return \Illuminate\Support\Facades\Crypt::decryptString($settings->platform_fonnte_api_key); } catch (\Exception $e) { return $settings->platform_fonnte_api_key; }
            })(),
            'watzap_key'    => (function() use ($settings) {
                try { return \Illuminate\Support\Facades\Crypt::decryptString($settings->platform_watzap_api_key); } catch (\Exception $e) { return $settings->platform_watzap_api_key; }
            })(),
            'watzap_number' => $settings->platform_watzap_number_key,
            'watsap_key'    => (function() use ($settings) {
                try { return \Illuminate\Support\Facades\Crypt::decryptString($settings->platform_watsap_api_key); } catch (\Exception $e) { return $settings->platform_watsap_api_key; }
            })(),
            'watsap_device' => $settings->platform_watsap_id_device,
        ];

        $jobs = [];
        foreach ($owners as $owner) {
            $jobs[] = new SendSystemBroadcastJob(
                $this->broadcast, 
                $owner, 
                $waActive, 
                $waProvider, 
                $waSettingsData
            );
        }

        $broadcastId = $this->broadcast->id;

        \Illuminate\Support\Facades\Bus::batch($jobs)
            ->then(function (\Illuminate\Bus\Batch $batch) use ($broadcastId) {
                $broadcast = SystemBroadcast::find($broadcastId);
                if ($broadcast) {
                    $broadcast->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'success_count' => \App\Models\SystemBroadcastLog::where('system_broadcast_id', $broadcastId)->where('status', 'sent')->count(),
                        'failure_count' => \App\Models\SystemBroadcastLog::where('system_broadcast_id', $broadcastId)->where('status', 'failed')->count(),
                    ]);
                }
            })
            ->catch(function (\Illuminate\Bus\Batch $batch, \Throwable $e) use ($broadcastId) {
                Log::error("System Broadcast Batch Failed for ID {$broadcastId}: " . $e->getMessage());
            })
            ->finally(function (\Illuminate\Bus\Batch $batch) use ($broadcastId) {
                // Final update if needed
            })
            ->name("System Broadcast: {$this->broadcast->subject}")
            ->dispatch();
    }

    /**
     * Remove redundant helper methods as they are now in SendSystemBroadcastJob
     */
}

