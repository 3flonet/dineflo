<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\QueuePromotion;
use App\Models\AppFeature;
use App\Models\WithdrawRequest;
use App\Models\SupportTicket;
use App\Settings\GeneralSettings;

class PruneUnusedStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:prune-storage {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unused image/file assets from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting storage pruning...');

        $usedFiles = $this->collectUsedFiles();
        $this->info('Collected ' . count($usedFiles) . ' unique files used in database.');

        $directories = [
            'restaurants/logos',
            'restaurants/logos_square',
            'restaurants/covers',
            'menu-items',
            'menu-categories',
            'queue-promotions',
            'features',
            'settings',
            'support-attachments',
            'withdraw-receipts',
        ];

        $deletedCount = 0;
        $savedSpace = 0;

        foreach ($directories as $directory) {
            $files = Storage::disk('public')->allFiles($directory);
            $this->info("Scanning directory: storage/app/public/{$directory} (" . count($files) . " files found)");

            foreach ($files as $file) {
                // Skip if file is used in database
                if (in_array($file, $usedFiles)) {
                    continue;
                }

                // Safety: Skip files modified in the last 1 hour (might be currently uploading)
                $lastModified = Storage::disk('public')->lastModified($file);
                if (time() - $lastModified < 3600) {
                    continue;
                }

                $size = Storage::disk('public')->size($file);
                
                if ($this->option('force') || $this->confirm("Delete unused file: {$file} (" . round($size / 1024, 2) . " KB)?", true)) {
                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                    $savedSpace += $size;
                }
            }
        }

        $this->info("Pruning finished. Removed {$deletedCount} files, saved " . round($savedSpace / 1024 / 1024, 2) . " MB.");
    }

    private function collectUsedFiles()
    {
        $usedFiles = [];

        // 1. General Settings
        $settings = app(GeneralSettings::class);
        $settingsFields = ['site_logo', 'site_favicon', 'site_og_image', 'pwa_icon_192', 'pwa_icon_512'];
        foreach ($settingsFields as $field) {
            if (!empty($settings->$field)) {
                $usedFiles[] = $settings->$field;
            }
        }

        // 2. Restaurants
        $restaurantImages = DB::table('restaurants')
            ->select('logo', 'logo_square', 'cover_image')
            ->get();
        foreach ($restaurantImages as $res) {
            if ($res->logo) $usedFiles[] = $res->logo;
            if ($res->logo_square) $usedFiles[] = $res->logo_square;
            if ($res->cover_image) $usedFiles[] = $res->cover_image;
        }

        // 3. Menu Items
        $menuItemImages = DB::table('menu_items')->whereNotNull('image')->pluck('image')->toArray();
        $usedFiles = array_merge($usedFiles, $menuItemImages);

        // 4. Menu Categories
        $menuCategoryImages = DB::table('menu_categories')->whereNotNull('image')->pluck('image')->toArray();
        $usedFiles = array_merge($usedFiles, $menuCategoryImages);

        // 5. Queue Promotions
        $promotionFiles = DB::table('queue_promotions')->whereNotNull('file_path')->pluck('file_path')->toArray();
        $usedFiles = array_merge($usedFiles, $promotionFiles);

        // 6. App Features
        $featureImages = DB::table('app_features')->whereNotNull('image_url')->pluck('image_url')->toArray();
        $usedFiles = array_merge($usedFiles, $featureImages);

        // 7. Withdraw Receipts
        $withdrawReceipts = DB::table('withdraw_requests')->whereNotNull('transfer_receipt_path')->pluck('transfer_receipt_path')->toArray();
        $usedFiles = array_merge($usedFiles, $withdrawReceipts);

        // 8. Support Ticket Attachments
        $ticketAttachments = DB::table('support_tickets')->whereNotNull('attachment')->pluck('attachment')->toArray();
        $usedFiles = array_merge($usedFiles, $ticketAttachments);

        // Normalize paths (some might have 'storage/' prefix or different slashes)
        return array_unique(array_filter(array_map(function($path) {
            $path = str_replace('storage/', '', $path);
            return ltrim($path, '/');
        }, $usedFiles)));
    }
}
