<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function isValid()
    {
        return $this->status === 'active' && ($this->expires_at->isFuture() || $this->expires_at === null);
    }
    
    public function activate($paymentId = null)
    {
        // Prevent double activation logic if already valid
        if ($this->isValid()) {
             return;
        }

        $durationDays = (int) $this->plan->duration_days;
        $isYearly     = ($this->billing_period === 'yearly');
        $price        = $isYearly ? $this->plan->yearly_price : $this->plan->price;

        $this->update([
            'status'     => 'active',
            'starts_at'  => now(),
            'expires_at' => $isYearly ? now()->addYear() : now()->addDays($durationDays),
        ]);
        
        // Create Invoice Record
        if ($paymentId) {
            $invoice = $this->invoices()->where('midtrans_id', $paymentId)->first();
            
            if (!$invoice) {
                $invoice = $this->invoices()->create([
                    'amount'      => $price,
                    'status'      => 'paid',
                    'paid_at'     => now(),
                    'midtrans_id' => $paymentId,
                ]);
            }
            
            // Send Automated Email Notification
            if ($invoice && $invoice->amount > 0) {
                 try {
                     \Illuminate\Support\Facades\Mail::to($this->user->email)->send(new \App\Mail\SubscriptionInvoiceMail($invoice));
                 } catch (\Exception $e) {
                     \Illuminate\Support\Facades\Log::error('Subscription Email Error: ' . $e->getMessage());
                 }
            }
        }
    }
}
