<?php

namespace App\Filament\Restaurant\Pages\Pos\Traits;

/**
 * Trait HasCashRegister
 *
 * Mengelola state dan logic POS Register Session (shift kasir)
 * dan Cash Drawer (laci uang).
 */
trait HasCashRegister
{
    // ─── State ───────────────────────────────────────────────────────────────

    public $activeSession    = null;
    public $showRegisterModal = false;
    public $openingCash      = 0;
    public $closingCash      = 0;
    public $showAdjustmentModal = false;
    public $adjustmentAmount  = 0;
    public $adjustmentType    = 'in'; // 'in' | 'out' | 'open'
    public $adjustmentReason  = '';
    public $cashReceived      = 0;

    // ─── Cash Input Helpers ───────────────────────────────────────────────────

    public function addCash($amount)
    {
        $current = floatval(preg_replace('/[^0-9]/', '', $this->cashReceived ?: 0));
        $this->cashReceived = $current + $amount;
    }

    public function updatedCashReceived($value)
    {
        if (!is_numeric($value)) {
            $this->cashReceived = preg_replace('/[^0-9]/', '', $value);
        }
    }

    // ─── Register Session ─────────────────────────────────────────────────────

    public function openRegister()
    {
        $tenantId = \Filament\Facades\Filament::getTenant()->id;

        $this->activeSession = \App\Models\PosRegisterSession::create([
            'restaurant_id' => $tenantId,
            'user_id'       => auth()->id(),
            'opening_cash'  => $this->openingCash,
            'expected_cash' => $this->openingCash,
            'status'        => 'open',
            'opened_at'     => now(),
        ]);

        $this->showRegisterModal = false;

        \Filament\Notifications\Notification::make()
            ->title('Register Opened')
            ->body('Shift started with modal Rp ' . number_format($this->openingCash, 0, ',', '.'))
            ->success()
            ->send();
    }

    public function closeRegister()
    {
        if (!$this->activeSession) return;

        $session = \App\Models\PosRegisterSession::find($this->activeSession->id);
        $session->update([
            'closing_cash' => $this->closingCash,
            'status'       => 'closed',
            'closed_at'    => now(),
        ]);

        $this->activeSession   = null;
        $this->showRegisterModal = false;

        \Filament\Notifications\Notification::make()
            ->title('Register Closed')
            ->body('Shift ended. Reconciliation report generated.')
            ->success()
            ->send();

        return redirect()->route('filament.restaurant.pages.pos', ['tenant' => \Filament\Facades\Filament::getTenant()]);
    }

    // ─── Cash Adjustment ──────────────────────────────────────────────────────

    public function adjustCash()
    {
        if (!$this->activeSession) return;

        if (empty(trim($this->adjustmentReason))) {
            \Filament\Notifications\Notification::make()
                ->title('Gagal')
                ->body('Alasan wajib diisi untuk audit laci.')
                ->danger()
                ->send();
            return;
        }

        $amount = (float) $this->adjustmentAmount;

        if ($this->adjustmentType !== 'open' && $amount <= 0) {
            \Filament\Notifications\Notification::make()
                ->title('Gagal')
                ->body('Nominal uang harus lebih dari 0.')
                ->danger()
                ->send();
            return;
        }

        if ($this->adjustmentType === 'out') {
            $amount = -$amount;
        } elseif ($this->adjustmentType === 'open') {
            $amount = 0;
        }

        $reasonPrefix = match ($this->adjustmentType) {
            'in'   => 'CASH IN: ',
            'out'  => 'CASH OUT: ',
            'open' => 'MANUAL OPEN: ',
        };

        \App\Models\CashDrawerLog::create([
            'restaurant_id'         => \Filament\Facades\Filament::getTenant()->id,
            'user_id'               => auth()->id(),
            'pos_register_session_id' => $this->activeSession->id,
            'type'                  => 'manual',
            'amount'                => $amount,
            'reason'                => $reasonPrefix . $this->adjustmentReason,
        ]);

        if ($this->adjustmentType !== 'open' && $amount !== 0.0) {
            $session = \App\Models\PosRegisterSession::find($this->activeSession->id);
            $session->increment('expected_cash', $amount);
            $this->activeSession = $session;
        }

        $this->showAdjustmentModal = false;
        $this->adjustmentAmount    = 0;
        $this->adjustmentReason    = '';

        \Filament\Notifications\Notification::make()
            ->title($this->adjustmentType === 'open' ? 'Laci Terbuka' : 'Kas Disesuaikan')
            ->success()
            ->send();

        $this->dispatch('open-cash-drawer-pulse');
    }

    // ─── Manual Cash Drawer Open ──────────────────────────────────────────────

    public function openCashDrawer($reason = 'Manual Open')
    {
        if (!auth()->user()->hasFeature('Cash Drawer Integration')) {
            \Filament\Notifications\Notification::make()
                ->title('Feature Locked')
                ->body('Cash Drawer Integration is an Empire feature.')
                ->danger()
                ->send();
            return;
        }

        \App\Models\CashDrawerLog::create([
            'restaurant_id' => \Filament\Facades\Filament::getTenant()->id,
            'user_id'       => auth()->id(),
            'type'          => 'manual',
            'reason'        => $reason,
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Cash Drawer Opened')
            ->success()
            ->send();

        $this->dispatch('open-cash-drawer-pulse');
    }
}
