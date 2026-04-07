<?php

namespace App\Livewire\Public;

use App\Settings\GeneralSettings;
use Livewire\Component;

use App\Models\AppFeature;

class FeaturesPage extends Component
{
    public function render(GeneralSettings $settings)
    {
        $features = AppFeature::orderBy('order_index')
            ->get();

        $categoryMeta = [
            'order' => ['icon' => 'shopping-cart', 'title' => 'Sistem Pemesanan QR'],
            'kitchen' => ['icon' => 'hat-chef', 'title' => 'Kitchen Display System (KDS)'],
            'pos' => ['icon' => 'credit-card', 'title' => 'POS Internal & Kasir'],
            'kiosk' => ['icon' => 'smartphone', 'title' => 'Self-Service Kiosk'],
            'loyalty' => ['icon' => 'heart', 'title' => 'Loyalitas & Marketing'],
            'finance' => ['icon' => 'cash-register', 'title' => 'Keuangan & Ledger'],
            'analytics' => ['icon' => 'stats', 'title' => 'Analitik & Laporan'],
            'notif' => ['icon' => 'bell-concierge', 'title' => 'Notifikasi & Real-time'],
            'pwa' => ['icon' => 'smartphone', 'title' => 'Progressive Web App (PWA)'],
            'support' => ['icon' => 'settings', 'title' => 'Pusat Bantuan & Support'],
            'admin' => ['icon' => 'settings', 'title' => 'Admin & Manajemen Sistem'],
        ];

        $sections = [];
        // Directly group the features collection by the 'tab' field
        $groupedFeatures = $features->groupBy('tab');

        foreach ($categoryMeta as $tabKey => $meta) {
            // Get the features for the current category key (tab name)
            $catFeatures = $groupedFeatures->get($tabKey);

            if ($catFeatures && $catFeatures->count() > 0) {
                $sections[] = [
                    'tab' => $tabKey,
                    'icon' => $meta['icon'],
                    'title' => $meta['title'],
                    'badge' => $catFeatures->contains('badge', 'Premium') ? 'Premium' : 'Standar',
                    'cards' => $catFeatures->sortBy('order_index')->values()->map(function ($f) {
                        $bullets = (array)$f->bullets;
                        $normalizedBullets = [];
                        foreach ($bullets as $b) {
                            if (is_array($b) && isset($b['bullet'])) {
                                $normalizedBullets[] = [
                                    'text' => $b['bullet'],
                                    'icon' => $b['icon'] ?? 'star'
                                ];
                            } elseif (is_string($b)) {
                                $normalizedBullets[] = [
                                    'text' => $b,
                                    'icon' => 'star'
                                ];
                            }
                        }
                        return [
                            'title' => $f->title,
                            'desc' => $f->short_description,
                            'bullets' => $normalizedBullets,
                            'badge' => $f->badge,
                            'slug' => $f->slug,
                        ];
                    })->toArray()
                ];
            }
        }


        return view('livewire.public.features', [
            'settings' => $settings,
            'sections' => $sections,
            'totalFeatures' => $features->count(),
        ])->layout('components.layouts.app', ['hideLayoutFooter' => true]);
    }
}
