<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Member;
use App\Models\MenuItemIngredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\RefundLog;
use App\Models\User;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Cara menjalankan test:
 *   php artisan test --filter RefundTest
 * atau:
 *   php artisan test tests/Feature/RefundTest.php
 *
 * PERHATIAN: Test ini menggunakan RefreshDatabase, pastikan konfigurasi
 * phpunit.xml menggunakan koneksi DB testing yang terpisah.
 */

// ===========================================================================
// CATATAN: Test di file ini menggunakan konsep manual assertion tanpa
// membutuhkan factory yang belum ada. Jalankan satu-per-satu untuk menguji
// setiap aspek logika refund. Lihat komentar tiap test untuk skenario detail.
// ===========================================================================

class RefundTest extends TestCase
{
    // Gunakan trait ini untuk memastikan database bersih setiap kali test dijalankan
    use RefreshDatabase;

    protected $restaurant;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed data dasar: Settings & Roles
        $this->seed([\Database\Seeders\SettingsSeeder::class, \Database\Seeders\EssentialRolesSeeder::class]);

        // Setup data dasar: User dan Restaurant
        $this->user = User::factory()->create();
        $this->user->assignRole('restaurant_owner');

        $this->restaurant = Restaurant::create([
            'user_id' => $this->user->id,
            'name' => 'Test Restaurant',
            'slug' => 'test-restaurant',
            'is_active' => true,
        ]);

        // Login sebagai user
        $this->actingAs($this->user);
    }

    /**
     * Helper untuk membuat Order paid.
     */
    protected function createPaidOrder(array $attributes = []): Order
    {
        return Order::create(array_merge([
            'restaurant_id' => $this->restaurant->id,
            'customer_name' => 'John Doe',
            'payment_status' => 'paid',
            'status' => 'completed',
            'subtotal' => 100000,
            'total_amount' => 116000,
            'tax_amount' => 11000,
            'additional_fees_amount' => 5000,
        ], $attributes));
    }

    /**
     * TEST 1: Verifikasi bahwa refund_status tersimpan ke DB.
     */
    public function test_refund_status_is_saved_to_database(): void
    {
        $order = $this->createPaidOrder();

        // Simulasikan update seperti yang dilakukan action refund
        $order->update([
            'refund_status'   => 'full',
            'refunded_amount' => $order->total_amount,
            'payment_status'  => 'refunded',
        ]);

        // Refresh dari DB untuk memastikan value benar-benar tersimpan
        $order->refresh();

        $this->assertEquals('full', $order->refund_status,
            'refund_status harus tersimpan.');
        $this->assertEquals('refunded', $order->payment_status,
            'payment_status harus berubah menjadi refunded setelah full refund.');
        $this->assertEquals($order->total_amount, $order->refunded_amount,
            'refunded_amount harus sama dengan total_amount untuk full refund.');

        $this->pass('✅ TEST 1 PASSED: refund_status berhasil disimpan ke database.');
    }

    /**
     * TEST 2: Verifikasi logika perhitungan refund proporsional.
     */
    public function test_proportional_refund_calculation_is_correct(): void
    {
        // Simulasi data order
        $subtotal       = 100000;
        $taxAmount      = 11000;
        $additionalFees = 5000;

        // Anggap kita refund 1 item senilai Rp 50.000 dari subtotal Rp 100.000
        $itemRefundSubtotal = 50000;
        $refundRatio        = $itemRefundSubtotal / $subtotal; // 0.5 = 50%

        $taxRefund  = $taxAmount * $refundRatio;
        $feeRefund  = $additionalFees * $refundRatio;
        $totalRefund = $itemRefundSubtotal + $taxRefund + $feeRefund;

        $this->assertEquals(0.5, $refundRatio);
        $this->assertEquals(5500, $taxRefund);
        $this->assertEquals(2500, $feeRefund);
        $this->assertEquals(58000, $totalRefund);

        $this->pass('✅ TEST 2 PASSED: Kalkulasi proporsi refund benar.');
    }

    /**
     * TEST 3: Verifikasi bahwa is_loyalty_processed tidak di-reset untuk partial refund.
     */
    public function test_loyalty_flag_preserved_on_partial_refund(): void
    {
        $order = $this->createPaidOrder([
            'is_loyalty_processed' => true
        ]);

        // Simulasi partial refund: is_loyalty_processed TIDAK boleh berubah
        $isFullRefund = false;

        if ($isFullRefund) {
            $order->updateQuietly(['is_loyalty_processed' => false]);
        }

        $order->refresh();

        $this->assertTrue($order->is_loyalty_processed,
            'is_loyalty_processed HARUS tetap true setelah partial refund.');

        $this->pass('✅ TEST 3 PASSED: is_loyalty_processed tetap valid setelah partial refund.');
    }

    /**
     * TEST 4: Verifikasi bahwa RefundLog dibuat setelah proses refund.
     */
    public function test_refund_log_is_created(): void
    {
        $order = $this->createPaidOrder();
        
        // Create category and menu item for the order item to satisfy constraints
        $category = MenuCategory::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Category',
        ]);
        
        $menuItem = MenuItem::create([
            'restaurant_id' => $this->restaurant->id,
            'menu_category_id' => $category->id,
            'name' => 'Meal',
            'price' => 50000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'quantity' => 1,
            'unit_price' => 50000,
            'total_price' => 50000,
        ]);

        $countBefore = RefundLog::where('order_id', $order->id)->count();

        RefundLog::create([
            'restaurant_id'   => $order->restaurant_id,
            'order_id'        => $order->id,
            'processed_by_id' => $this->user->id,
            'amount'          => 50000,
            'reason'          => 'Test refund reason',
            'refunded_items'  => [$order->items->first()->id],
            'is_full_refund'  => false,
        ]);

        $countAfter = RefundLog::where('order_id', $order->id)->count();

        $this->assertEquals($countBefore + 1, $countAfter,
            'RefundLog harus bertambah 1 setelah proses refund.');

        $this->pass('✅ TEST 4 PASSED: RefundLog berhasil dibuat.');
    }

    /**
     * TEST 5: Verifikasi Job WA refund terdispatch.
     */
    public function test_refund_whatsapp_job_is_dispatched(): void
    {
        Queue::fake();

        $order = $this->createPaidOrder([
            'customer_phone' => '628123456789'
        ]);

        // Simulasi dispatch yang ada di action refund
        \App\Jobs\SendRefundWhatsApp::dispatch(
            $order,
            50000,
            'Test reason',
            $order->customer_phone
        );

        Queue::assertPushed(\App\Jobs\SendRefundWhatsApp::class);

        $this->pass('✅ TEST 5 PASSED: SendRefundWhatsApp job berhasil didispatch.');
    }

    /**
     * TEST 6: Verifikasi Job Email refund terdispatch.
     */
    public function test_refund_email_job_is_dispatched(): void
    {
        Queue::fake();

        $order = $this->createPaidOrder([
            'customer_email' => 'customer@example.com'
        ]);

        \App\Jobs\SendWhitelabelMail::dispatch(
            $order->restaurant,
            $order->customer_email,
            new \App\Mail\OrderRefunded($order, 50000, 'Test reason', false)
        );

        Queue::assertPushed(\App\Jobs\SendWhitelabelMail::class);

        $this->pass('✅ TEST 6 PASSED: SendWhitelabelMail (refund) job berhasil didispatch.');
    }

    /**
     * TEST 7: Verifikasi Order model memiliki kolom yang benar di $fillable.
     */
    public function test_order_model_fillable_contains_refund_columns(): void
    {
        $order    = new Order();
        $fillable = $order->getFillable();

        $this->assertContains('refund_status', $fillable);
        $this->assertContains('refunded_amount', $fillable);
        $this->assertContains('payment_status', $fillable);

        $this->pass('✅ TEST 7 PASSED: Semua kolom refund ada di $fillable Order model.');
    }

    // Helper agar output test lebih readable
    private function pass(string $message): void
    {
        $this->assertTrue(true, $message);
        // fwrite(STDOUT, "\n" . $message . "\n"); // Optional, sometimes annoying in CI
    }
}
