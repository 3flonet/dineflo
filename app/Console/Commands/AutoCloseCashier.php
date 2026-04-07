<?php

namespace App\Console\Commands;

use App\Models\PosRegisterSession;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCloseCashier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-close-cashier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically closes open POS register sessions 1 hour after restaurant closing time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for POS sessions to auto-close...');

        $activeSessions = PosRegisterSession::where('status', 'open')->get();
        $closedCount = 0;

        foreach ($activeSessions as $session) {
            $restaurant = $session->restaurant;

            if (!$restaurant || !$restaurant->auto_close_cashier) {
                continue;
            }

            $openedAt = $session->opened_at;
            // Get day of the week when session was opened (e.g. 'monday')
            $dayOfWeek = strtolower($openedAt->format('l'));
            
            $openingHours = collect($restaurant->opening_hours)->firstWhere('day', $dayOfWeek);

            if (!$openingHours || $openingHours['is_closed']) {
                // If no hours set or marked closed, we fallback to midnight check? 
                // Or just ignore. Usually they shouldn't be able to open a session if closed.
                continue;
            }

            try {
                $closeTimeStr = $openingHours['close']; // format 'HH:mm'
                
                // Create a Carbon instance for the closing time on the day the session was opened
                $closeDateTime = $openedAt->copy()->setTimeFromTimeString($closeTimeStr);

                // If closing time is earlier than opening time (e.g. opens 08:00, closes 02:00), 
                // it means it closes the NEXT day
                $openTimeStr = $openingHours['open'];
                if (Carbon::parse($closeTimeStr)->lt(Carbon::parse($openTimeStr))) {
                    $closeDateTime->addDay();
                }

                // Threshold is 1 hour after closing
                $autoCloseThreshold = $closeDateTime->addHour();

                if (now()->greaterThanOrEqualTo($autoCloseThreshold)) {
                    $this->info("Dispatching auto-close job for session #{$session->id} for restaurant: {$restaurant->name}");
                    
                    \App\Jobs\CloseCashierSessionJob::dispatch($session);

                    $closedCount++;
                }
            } catch (\Exception $e) {
                Log::error("Error processing auto-close logic for session #{$session->id}: " . $e->getMessage());
                $this->error("Error for session #{$session->id}: " . $e->getMessage());
            }
        }

        $this->info("Finished. Closed {$closedCount} sessions.");
    }
}
