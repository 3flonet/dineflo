<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Settings\GeneralSettings;

Route::get('/manifest.json', [App\Http\Controllers\PWAManifestController::class, 'manifest'])->name('pwa.manifest');

Route::get('/', App\Livewire\Public\LandingPage::class)->name('home');
Route::get('/features', App\Livewire\Public\FeaturesPage::class)->name('features');
Route::get('/features/{feature:slug}', App\Livewire\Public\FeatureDetail::class)->name('features.show');

// PWA Offline Route
Route::get('/consultation', App\Livewire\Public\ConsultationPage::class)->name('consultation');
Route::get('/community', App\Livewire\Public\SocialWall::class)->name('community');
Route::view('/offline', 'offline');

// System Health Check
Route::get('/up', [App\Http\Controllers\HealthCheckController::class, 'index'])->name('health.check');

// Web Installer
Route::prefix('install')->group(function () {
    Route::get('/', [App\Http\Controllers\InstallerController::class, 'index'])->name('install.index');
    Route::post('/requirements', [App\Http\Controllers\InstallerController::class, 'checkRequirements']);
    Route::post('/database', [App\Http\Controllers\InstallerController::class, 'setupDatabase']);
    Route::post('/license', [App\Http\Controllers\InstallerController::class, 'verifyLicense']);
    Route::post('/migrate', [App\Http\Controllers\InstallerController::class, 'runMigrations']);
    Route::post('/finalize', [App\Http\Controllers\InstallerController::class, 'finalize']);
});



// Public Pages
Route::get('/restaurants', App\Livewire\Public\RestaurantList::class)->name('frontend.restaurants.index');
Route::get('/resto/{restaurant:slug}', App\Livewire\Public\RestaurantProfile::class)->name('frontend.restaurants.show');

// WebPush Subscription
Route::post('/push-subscription', [App\Http\Controllers\PushSubscriptionController::class, 'store'])->middleware('auth');

Route::get('restaurant/{restaurant:slug}', App\Livewire\Restaurant\Menu::class)->name('restaurant.index');
Route::get('restaurant/{restaurant:slug}/table/{qr_code}', App\Livewire\Restaurant\Menu::class)->name('restaurant.table');
Route::get('restaurant/{restaurant:slug}/cart', App\Livewire\Restaurant\Cart::class)->name('restaurant.cart');
Route::get('restaurant/{restaurant:slug}/reserve', App\Livewire\Restaurant\ReservationForm::class)->name('restaurant.reserve');

// Order Payment & Summary
Route::get('order/{order}', App\Livewire\Payment\OrderSummary::class)->name('order.summary');

// Kiosk Mode
Route::get('restaurant/{restaurant:slug}/kiosk', App\Livewire\Restaurant\Kiosk::class)->name('restaurant.kiosk');
Route::get('restaurant/{restaurant:slug}/display', App\Livewire\Restaurant\QueueDisplay::class)->name('restaurant.queue_display');

// Public Order Receipt (No Auth)
Route::get('/orders/{order}/receipt', [App\Http\Controllers\OrderPublicReceiptController::class, 'show'])->name('order.public_receipt');
Route::get('/orders/{hash}/review', App\Livewire\Restaurant\FeedbackForm::class)->name('order.feedback');
Route::get('/orders/{hash}/track', App\Livewire\Public\OrderTracking::class)->name('order.track');
Route::get('/reservations/{hash}/track', App\Livewire\Public\ReservationTracking::class)->name('reservations.track');

// Print Receipt
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/{order}/print', [App\Http\Controllers\OrderPrintController::class, 'print'])->name('order.print');
    Route::get('/orders/{order}/invoice', [App\Http\Controllers\OrderPrintController::class, 'downloadPdf'])->name('order.download.invoice');

    // Subscription Invoices
    Route::get('/subscription-invoices/{invoice}/download', [App\Http\Controllers\SubscriptionInvoiceController::class, 'download'])->name('subscription.invoice.download');

    // Report Export - Updated to include Tenant Parameter
    Route::get('restaurant/{restaurant:slug}/reports/export', [App\Http\Controllers\Restaurant\ReportExportController::class, 'export'])->name('reports.export');
    Route::get('restaurant/{restaurant:slug}/reports/export-pdf', [App\Http\Controllers\Restaurant\ReportExportController::class, 'exportPdf'])->name('reports.export_pdf');
    Route::get('restaurant/{restaurant:slug}/reports/export-excel', [App\Http\Controllers\Restaurant\ReportExportController::class, 'exportExcel'])->name('reports.export_excel');

    // POS Offline Sync
    Route::post('/pos/offline-sync', [App\Http\Controllers\POS\OfflineSyncController::class, 'sync'])->name('pos.offline_sync');

    // QR Card Print Routes
    Route::get(
        'restaurant/{restaurant:slug}/tables/{table}/qr-print',
        [App\Http\Controllers\Restaurant\QrCardController::class, 'single']
    )->name('restaurant.tables.qr-print');

    Route::get(
        'restaurant/{restaurant:slug}/tables/qr-bulk-print',
        [App\Http\Controllers\Restaurant\QrCardController::class, 'bulk']
    )->name('restaurant.tables.qr-bulk-print');

    // QR Card Save Design Route
    Route::post(
        'restaurant/{restaurant:slug}/tables/qr-designer/save',
        [App\Http\Controllers\Restaurant\QrCardController::class, 'saveDesign']
    )->name('restaurant.tables.qr-save');

    Route::get(
        'restaurant/{restaurant:slug}/tables/qr-designer',
        [App\Http\Controllers\Restaurant\QrCardController::class, 'designer']
    )->name('restaurant.tables.qr-designer');

    // License Management
    Route::get('/admin/license', [App\Http\Controllers\Admin\LicenseController::class, 'index'])->name('admin.license');
    Route::post('/admin/license/sync', [App\Http\Controllers\Admin\LicenseController::class, 'sync'])->name('admin.license.sync');
    Route::post('/admin/license/activate', [App\Http\Controllers\Admin\LicenseController::class, 'activate'])->name('admin.license.activate');
});

// Kitchen Display System
Route::get('restaurant/{restaurant:slug}/kitchen', App\Livewire\Kitchen\Board::class)->name('restaurant.kitchen');

// Service / Staff Panel
Route::get('restaurant/{restaurant:slug}/service', App\Livewire\Staff\OrderList::class)->name('restaurant.service');

// Midtrans Webhook
Route::post('payment/notification', [App\Http\Controllers\MidtransController::class, 'notificationHandler']);

// Email Marketing Tracking
Route::get('/m/t/{hash}', [App\Http\Controllers\MarketingTrackingController::class, 'pixel'])->name('marketing.tracking');

// ── Member Self-Service Portal ────────────────────────────────────────────
Route::prefix('m/{restaurant:slug}')->group(function () {
    Route::get('/',          App\Livewire\Member\MemberLogin::class)->name('member.portal.login');
    Route::get('/otp',       App\Livewire\Member\MemberOtp::class)->name('member.portal.otp');
    Route::get('/dashboard', App\Livewire\Member\MemberDashboard::class)->name('member.portal.dashboard');
});

// Debug routes hanya aktif di environment local
if (app()->isLocal()) {
    require base_path('routes/debug.php');
}
