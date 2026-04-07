<?php

namespace App\Jobs;

use App\Models\PosRegisterSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CloseCashierSessionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public PosRegisterSession $session)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->session->status !== 'open') {
            return;
        }

        try {
            $this->session->update([
                'closing_cash' => $this->session->expected_cash,
                'status' => 'closed',
                'closed_at' => now(),
                'notes' => ($this->session->notes ? $this->session->notes . "\n" : "") . "Ditutup otomatis oleh sistem (1 Jam setelah jam operasional berakhir).",
            ]);
            
            Log::info("Auto-closed session #{$this->session->id}");
        } catch (\Exception $e) {
            Log::error("Failed to auto-close session #{$this->session->id}: " . $e->getMessage());
        }
    }
}
