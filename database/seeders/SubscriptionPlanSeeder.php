<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        // 1. STARTER (Free / Trial)
        $starter = SubscriptionPlan::where('slug', 'free')->first() ?? SubscriptionPlan::where('slug', 'starter')->first() ?? new SubscriptionPlan();
        $starter->fill([
            'slug'           => 'starter',
            'name'           => 'Starter',
            'description'    => 'Cocok untuk UMKM & warung yang baru mulai digital',
            'price'          => 0,
            'duration_days'  => 365,
            'billing_period' => 'monthly',
            'features'       => ['QR Code Ordering', 'Basic Sales Reports'],
            'limits'         => [
                'max_restaurants' => 1,
                'max_menus'       => 15,
                'max_orders'      => 100,
                'max_members'     => 0,
            ],
            'is_active'       => true,
            'is_highlighted'  => false,
        ])->save();

        // 2. ESSENTIAL (Basic)
        $essential = SubscriptionPlan::where('slug', 'basic')->first() ?? SubscriptionPlan::where('slug', 'essential')->first() ?? new SubscriptionPlan();
        $essential->fill([
            'slug'           => 'essential',
            'name'           => 'Essential',
            'description'    => 'Ideal untuk restoran & kafe skala menengah',
            'price'          => 75000,
            'duration_days'  => 30,
            'billing_period' => 'monthly',
            'features'       => [
                'QR Code Ordering',
                'Sales Reports',
                'POS System',
                'Kitchen Display System',
                'Waiter Call System',
                'Payment Gateway',
                'Basic Inventory',
                'Table Management System',
                'Queue Management System',
            ],
            'limits'         => [
                'max_restaurants' => 1,
                'max_menus'       => -1,
                'max_orders'      => -1,
                'max_members'     => 0,
            ],
            'is_active'      => true,
            'is_highlighted' => false,
        ])->save();

        // 3. PREMIUM (Pro) — Paling Laris
        $premium = SubscriptionPlan::where('slug', 'pro')->first() ?? SubscriptionPlan::where('slug', 'premium')->first() ?? new SubscriptionPlan();
        $premium->fill([
            'slug'           => 'premium',
            'name'           => 'Premium',
            'description'    => 'Terlengkap — untuk restoran yang ingin tumbuh cepat',
            'price'          => 175000,
            'duration_days'  => 30,
            'billing_period' => 'monthly',
            'features'       => [
                'QR Code Ordering',
                'Sales Reports',
                'POS System',
                'Kitchen Display System',
                'Waiter Call System',
                'Payment Gateway',
                'Membership & Loyalty',
                'WhatsApp Marketing',
                'Email Marketing',
                'Smart Upselling',
                'Kiosk Mode',
                'Inventory Level 2',
                'Expense Management',
                'Split Bill',
                'Multi-Restaurant Support',
                'Remove Branding',
                'Dynamic Pricing',
                'Customer Feedback & Ratings',
                'Feedback Reward Automation',
                'Payment Gateway Withdraw',
                'Profit Margin Insights',
                'Gift Cards',
                'Table Management System',
                'Queue Management System',
                'EDC Integration',
            ],
            'limits'         => [
                'max_restaurants' => -1,
                'max_menus'       => -1,
                'max_orders'      => -1,
                'max_members'     => 1000,
            ],
            'is_active'      => true,
            'is_highlighted' => true, // Badge "PALING LARIS"
        ])->save();

        // 4. EMPIRE (Enterprise)
        SubscriptionPlan::updateOrCreate(
            ['slug' => 'empire'],
            [
                'name'           => 'Empire Strategy',
                'description'    => 'Solusi enterprise untuk jaringan franchise & multi-cabang',
                'price'          => 500000,
                'duration_days'  => 30,
                'billing_period' => 'monthly',
                'features'       => [
                'QR Code Ordering',
                'Sales Reports',
                'POS System',
                'Kitchen Display System',
                'Waiter Call System',
                'Payment Gateway',
                'Membership & Loyalty',
                'WhatsApp Marketing',
                'Email Marketing',
                'Smart Upselling',
                'Kiosk Mode',
                'Inventory Level 2',
                'Expense Management',
                'Split Bill',
                'Multi-Restaurant Support',
                'Remove Branding',
                'Dynamic Pricing',
                'Customer Feedback & Ratings',
                'Feedback Reward Automation',
                'Payment Gateway Withdraw',
                'Profit Margin Insights',
                'Advanced Kitchen Analytics',
                'Staff Performance Tracking',
                'Refund Handling',
                'Loss Prevention',
                'Cash Drawer Integration',
                'Split Bill by Item',
                'Dashboard HQ',
                'Priority Support',
                'Gift Cards',
                'Table Management System',
                'Queue Management System',
                'EDC Integration',
            ],
                'limits'         => [
                    'max_restaurants' => -1,
                    'max_menus'       => -1,
                    'max_orders'      => -1,
                    'max_members'     => -1,
                ],
                'is_active'      => true,
                'is_highlighted' => false,
            ]
        );
    }
}

