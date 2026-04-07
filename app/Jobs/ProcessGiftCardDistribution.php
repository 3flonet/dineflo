<?php

namespace App\Jobs;

use App\Filament\Restaurant\Resources\GiftCardResource;
use App\Models\GiftCard;
use App\Models\Member;
use App\Models\Restaurant;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessGiftCardDistribution implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public function __construct(
        protected int    $restaurantId,
        protected array  $memberIds,
        protected array  $config,
        protected int    $triggeredBy, // user ID yang trigger
    ) {}

    public function handle(): void
    {
        $restaurant  = Restaurant::find($this->restaurantId);
        $triggerUser = User::find($this->triggeredBy);

        if (!$restaurant) return;

        $success = 0;
        $skipped = 0;
        $failed  = 0;

        $members = Member::whereIn('id', $this->memberIds)
            ->where('restaurant_id', $this->restaurantId)
            ->get();

        foreach ($members as $member) {
            try {
                // Skip jika sudah punya GC aktif & user memilih skip
                if ($this->config['skip_existing'] ?? false) {
                    $hasActive = GiftCard::where('restaurant_id', $this->restaurantId)
                        ->where('status', 'active')
                        ->where(function ($q) use ($member) {
                            if ($member->whatsapp) {
                                $q->orWhere('recipient_phone', $member->whatsapp);
                            }
                            if ($member->email) {
                                $q->orWhere('recipient_email', $member->email);
                            }
                        })
                        ->exists();

                    if ($hasActive) {
                        $skipped++;
                        continue;
                    }
                }

                // Tentukan jumlah berdasarkan config
                $amount = $this->resolveAmount($member);
                if (!$amount || $amount <= 0) {
                    $skipped++;
                    continue;
                }

                // Replace placeholder {name}
                $message = str_replace(
                    '{name}',
                    $member->name,
                    $this->config['personal_message'] ?? ''
                );

                // Buat Gift Card
                $card = GiftCard::create([
                    'restaurant_id'    => $this->restaurantId,
                    'created_by'       => $this->triggeredBy,
                    'code'             => GiftCard::generateCode(),
                    'recipient_name'   => $member->name,
                    'recipient_phone'  => $member->whatsapp,
                    'recipient_email'  => $member->email,
                    'personal_message' => $message ?: null,
                    'original_amount'  => $amount,
                    'remaining_balance'=> $amount,
                    'status'           => 'active',
                    'expires_at'       => $this->config['expires_at'] ?? null,
                ]);

                // Kirim notifikasi smart (WA + Email)
                GiftCardResource::dispatchGiftCardNotifications($card, $restaurant);

                $success++;
            } catch (\Exception $e) {
                \Log::error("GiftCard Distribution Error for member [{$member->id}]: " . $e->getMessage());
                $failed++;
            }
        }

        // Kirim notifikasi ringkasan ke user yang trigger
        if ($triggerUser) {
            $body = "✅ {$success} berhasil dibuat";
            if ($skipped > 0) $body .= " · ⏭️ {$skipped} dilewati";
            if ($failed  > 0) $body .= " · ❌ {$failed} gagal";

            Notification::make()
                ->success()
                ->title('Distribusi Gift Card Selesai!')
                ->body($body)
                ->sendToDatabase($triggerUser);
        }
    }

    protected function resolveAmount(Member $member): float
    {
        // Flat amount berlaku untuk semua (mode: all_members / select_members)
        if (isset($this->config['flat_amount'])) {
            return (float) $this->config['flat_amount'];
        }

        // Per-tier amount (mode: by_tier)
        $tier = strtolower($member->tier ?? 'bronze');
        return (float) ($this->config['tier_amounts'][$tier] ?? 0);
    }
}
