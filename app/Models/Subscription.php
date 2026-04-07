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
        // But allow update if status is not active or expired
        if ($this->isValid()) {
             // Maybe extend? For now just return.
             return;
        }

        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($this->plan->duration_days),
        ]);
        
        // Create Invoice Record
        if ($paymentId) {
            // Check for existing invoice with same payment ID to prevent duplicates
            $invoice = $this->invoices()->where('midtrans_id', $paymentId)->first();
            
            if (!$invoice) {
                $invoice = $this->invoices()->create([
                    'amount' => $this->plan->price,
                    'status' => 'paid',
                    'paid_at' => now(),
                    'midtrans_id' => $paymentId,
                ]);
            }
            
            // Send Automated Email Notification with Attachment
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
