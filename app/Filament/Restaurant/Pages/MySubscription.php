<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Pages\Page;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Services\MidtransService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class MySubscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static string $view = 'filament.pages.my-subscription';
    protected static ?string $navigationLabel = 'Langganan Saya';
    protected static ?string $navigationGroup = 'PENGATURAN TOKO';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'my-subscription';

    public static function canAccess(): bool
    {
        return auth()->user()->can('page_MySubscription') || auth()->user()->hasRole('restaurant_owner');
    }

    public $plans;
    public $currentSubscription;
    public $invoices;
    public $snapToken;
    public $billingPeriod = 'monthly'; // Opsi: monthly, yearly

    /** @var bool Apakah user yang sedang login adalah staff (bukan owner)? */
    public bool $isStaff = false;

    /**
     * Resolve owner restoran aktif.
     * Menggunakan pola yang sama dengan User::hasFeature() & getLimits().
     * Dipanggil sebagai method (bukan cached property) agar tetap fresh di setiap wire call.
     */
    protected function resolveOwner(): \App\Models\User
    {
        $user   = auth()->user();
        $tenant = \Filament\Facades\Filament::getTenant();

        if ($tenant && $user->restaurant_id === $tenant->id && $tenant->user_id !== $user->id) {
            return $tenant->owner ?? $user;
        }

        return $user;
    }

    public function mount()
    {
        $owner = $this->resolveOwner();

        // Set flag isStaff: true jika owner resolve ke user berbeda
        $this->isStaff = ($owner->id !== auth()->id());

        $this->plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        $this->loadCurrentSubscription();
        $this->loadInvoices();
    }

    public function loadCurrentSubscription()
    {
        // Tampilkan subscription dari owner, bukan dari user yang login
        $this->currentSubscription = $this->resolveOwner()->currentSubscription;
    }

    public function loadInvoices()
    {
        $ownerId = $this->resolveOwner()->id;

        $this->invoices = \App\Models\SubscriptionInvoice::whereIn(
            'subscription_id',
            \App\Models\Subscription::where('user_id', $ownerId)->pluck('id')
        )
        ->latest()
        ->get();
    }

    public function subscribe($planId)
    {
        // Staff tidak boleh melakukan subscribe atas nama owner
        if ($this->isStaff) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Hanya pemilik restoran yang dapat mengelola langganan.')
                ->danger()
                ->send();
            return;
        }

        $plan = SubscriptionPlan::findOrFail($planId);

        $currentSub = auth()->user()->currentSubscription;

        // Check if user is trying to subscribe to the same plan they already have active
        if ($currentSub && $currentSub->subscription_plan_id === $plan->id && $currentSub->isValid()) {
            $threshold = app(\App\Settings\GeneralSettings::class)->subscription_expiry_warning_days;

            // Allow renewal only if within threshold days of expiration
            $daysUntilExpiry = (int) now()->diffInDays($currentSub->expires_at, false);

            if ($daysUntilExpiry > $threshold) {
                Notification::make()
                    ->title('Paket Sudah Aktif')
                    ->body('Anda saat ini sudah berlangganan paket ' . $plan->name . '. Anda dapat memperbarui paket ini saat sisa waktu kurang dari ' . $threshold . ' hari.')
                    ->warning()
                    ->send();
                return;
            }
        }

        // Free Plan Logic
        if ($plan->price <= 0) {
            Subscription::create([
                'user_id'              => auth()->id(),
                'subscription_plan_id' => $plan->id,
                'billing_period'        => $this->billingPeriod,
                'status'               => 'active',
                'starts_at'            => now(),
                'expires_at'           => ($this->billingPeriod === 'yearly') ? now()->addYear() : now()->addDays((int) $plan->duration_days),
            ]);

            $this->loadCurrentSubscription();
            Notification::make()->title('Subscribed to Free Plan successfully!')->success()->send();
            return;
        }

        // Paid Plan Logic
        $subscription = Subscription::create([
            'user_id'              => auth()->id(),
            'subscription_plan_id' => $plan->id,
            'billing_period'       => $this->billingPeriod,
            'status'               => 'pending_payment',
            'starts_at'            => null,
            'expires_at'           => null,
        ]);

        try {
            $service = new MidtransService();
            $token   = $service->createSubscriptionSnapToken($subscription);

            if ($token) {
                $subscription->payment_token = $token;
                $subscription->save();

                $this->snapToken = $token;
                $this->dispatch('open-midtrans-payment', token: $token);
            } else {
                Notification::make()->title('Payment Error')->body('Could not generate payment token.')->danger()->send();
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans Subscription Snap Error: ' . $e->getMessage());
            Notification::make()->title('System Error')->body($e->getMessage())->danger()->send();
        }
    }

    public function handlePaymentSuccess($result)
    {
        $transactionId = $result['transaction_id'] ?? null;
        $orderId       = $result['order_id'] ?? null;

        $subscription = Subscription::where('midtrans_id', $orderId)->first();

        if ($subscription) {
            $subscription->activate($transactionId);

            $this->loadCurrentSubscription();
            $this->loadInvoices();
            Notification::make()->title('Subscription Activated!')->success()->send();

            return redirect()->route('filament.restaurant.pages.my-subscription', ['tenant' => filament()->getTenant()]);
        } else {
            Notification::make()->title('Error')->body('Subscription record not found for Order ID: ' . $orderId)->danger()->send();
        }
    }
}

