<?php

namespace App\Livewire\Restaurant;

use App\Models\Order;
use App\Models\OrderFeedback;
use Livewire\Attributes\Layout;
use Livewire\Component;

class FeedbackForm extends Component
{
    public $hash;
    public $order;
    public $rating = 5;
    public $comment;
    public $food_rating = 5;
    public $service_rating = 5;
    public $ambience_rating = 5;
    
    public $isSubmitted = false;
    public $alreadyReviewed = false;

    public function mount($hash)
    {
        $this->hash = $hash;
        $this->order = Order::where('feedback_hash', $hash)->first();

        if (!$this->order) {
            abort(404, 'Pesanan tidak ditemukan.');
        }

        // Check if already reviewed
        if (OrderFeedback::where('order_id', $this->order->id)->exists()) {
            $this->alreadyReviewed = true;
        }

        // Feature gating check from Owner
        if (!$this->order->restaurant->owner->hasFeature('Customer Feedback & Ratings')) {
            abort(403, 'Fitur feedback belum diaktifkan untuk restoran ini.');
        }
    }

    public function setRating($value)
    {
        $this->rating = $value;
    }

    public function submit()
    {
        if ($this->alreadyReviewed) {
            return;
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'food_rating' => 'required|integer|min:1|max:5',
            'service_rating' => 'required|integer|min:1|max:5',
            'ambience_rating' => 'required|integer|min:1|max:5',
        ]);

        // Rate Limiting: Max 2 reviews per minute from one IP
        $key = 'feedback:' . request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 2)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            $this->addError('comment', "Terlalu banyak permintaan. Silakan coba lagi dalam $seconds detik.");
            return;
        }

        // Sanitization
        $cleanComment = strip_tags($this->comment ?: '');

        $feedback = OrderFeedback::create([
            'restaurant_id' => $this->order->restaurant_id,
            'order_id' => $this->order->id,
            'rating' => $this->rating,
            'comment' => $cleanComment,
            'categories' => [
                'food' => $this->food_rating,
                'service' => $this->service_rating,
                'ambience' => $this->ambience_rating,
            ],
            'is_public' => true, 
        ]);

        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        // --- FEEDBACK REWARD LOGIC ---
        $restaurant = $this->order->restaurant;
        if ($restaurant->feedback_reward_enabled && $restaurant->owner->hasFeature('Feedback Reward Automation')) {
            $member = \App\Models\Member::where('restaurant_id', $restaurant->id)
                ->where('whatsapp', $this->order->customer_phone)
                ->first();

            if ($member) {
                $rewardValue = '';
                
                // 1. Process Reward
                if ($restaurant->feedback_reward_type === 'points') {
                    $points = $restaurant->feedback_reward_points ?: 0;
                    $member->increment('points_balance', $points);
                    $rewardValue = $points . ' Poin';
                } elseif ($restaurant->feedback_reward_type === 'voucher' && $restaurant->feedback_reward_discount_id) {
                    $discount = \App\Models\Discount::find($restaurant->feedback_reward_discount_id);
                    if ($discount && $discount->code) {
                        $rewardValue = $discount->code;
                    }
                }

                // 2. Send Notifications
                if ($rewardValue) {
                    $channel = $restaurant->feedback_notification_channel;
                    $customerName = $this->order->customer_name ?: 'Pelanggan';

                    // WhatsApp Notification
                    if (in_array($channel, ['whatsapp', 'both']) && $restaurant->wa_is_active) {
                        $waMessage = "Halo Kak *{$customerName}*,\n\n";
                        $waMessage .= "Terima kasih banyak atas ulasan Anda untuk *{$restaurant->name}*! 🙏\n\n";
                        $waMessage .= "Sebagai apresiasi, kami memberikan hadiah spesial:\n";
                        
                        if ($restaurant->feedback_reward_type === 'points') {
                            $waMessage .= "🎁 *+{$rewardValue} Loyalitas* telah ditambahkan ke akun Anda.\n";
                        } else {
                            $waMessage .= "🎁 *Voucher Diskon: {$rewardValue}*\n";
                        }
                        
                        $waMessage .= "\n_Terima kasih sekali lagi dan sampai jumpa kembali!_";
                        
                        \App\Services\WhatsApp\WhatsAppService::sendMessage($restaurant, $member->whatsapp, $waMessage);
                    }

                    // Email Notification
                    if (in_array($channel, ['email', 'both']) && $this->order->customer_email) {
                        \App\Jobs\SendWhitelabelMail::dispatch(
                            $restaurant,
                            $this->order->customer_email,
                            new \App\Mail\FeedbackRewardMail($restaurant, $restaurant->feedback_reward_type, $rewardValue, $customerName)
                        );
                    }
                }
            }
        }
        // --- END FEEDBACK REWARD LOGIC ---

        // Send Notification to Owner if rating is low (1-2 stars)
        if ($this->rating <= 2) {
            $owner = $this->order->restaurant->owner;
            \Filament\Notifications\Notification::make()
                ->title('⚠️ Rating Rendah Diterima!')
                ->body("Pesanan #{$this->order->order_number} mendapat rating {$this->rating} bintang. Mohon segera dicek.")
                ->danger()
                ->icon('heroicon-o-exclamation-triangle')
                ->sendToDatabase($owner);
        }

        $this->isSubmitted = true;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.restaurant.feedback-form');
    }
}
