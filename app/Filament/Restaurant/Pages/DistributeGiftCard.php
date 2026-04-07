<?php

namespace App\Filament\Restaurant\Pages;

use App\Filament\Restaurant\Resources\GiftCardResource;
use App\Jobs\ProcessGiftCardDistribution;
use App\Models\GiftCard;
use App\Models\Member;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class DistributeGiftCard extends Page
{
    protected static string $view = 'filament.restaurant.pages.distribute-gift-card';

    protected static ?string $navigationLabel = 'Distribusi Gift Card';
    protected static ?string $navigationIcon  = 'heroicon-o-paper-airplane';
    protected static ?string $title           = 'Distribusi Gift Card';

    // Sembunyikan dari navbar — diakses via GiftCardResource header action
    protected static bool $shouldRegisterNavigation = false;

    // ─── State ──────────────────────────────────────────────────────────────────

    public string $targetType     = 'single'; // single | all_members | by_tier | select_members

    // Single
    public string $singleName     = '';
    public string $singlePhone    = '';
    public string $singleEmail    = '';
    public float  $singleAmount   = 0;

    // Bulk shared
    public string $personalMessage = '';
    public string $expiresAt       = '';
    public float  $flatAmount      = 0;

    // By Tier
    public array  $selectedTiers  = [];
    public float  $bronzeAmount   = 0;
    public float  $silverAmount   = 0;
    public float  $goldAmount     = 0;

    // Select Members
    public string $memberSearch   = '';
    public array  $selectedMemberIds = [];

    // UI State
    public bool   $showConfirmModal   = false;
    public bool   $skipExisting       = false;  // pilihan user di modal duplikat
    public bool   $isProcessing       = false;
    public array  $previewData        = [];
    public array  $duplicateMembers   = [];

    // ─── Lifecycle ──────────────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->hasFeature('Gift Cards') && $user?->can('create_gift_card');
    }

    public function updatedTargetType(): void
    {
        $this->resetPreview();
        $this->selectedMemberIds = [];
        $this->memberSearch = '';
    }

    public function updatedSelectedTiers(): void   { $this->buildPreview(); }
    public function updatedBronzeAmount(): void     { $this->buildPreview(); }
    public function updatedSilverAmount(): void     { $this->buildPreview(); }
    public function updatedGoldAmount(): void       { $this->buildPreview(); }
    public function updatedFlatAmount(): void       { $this->buildPreview(); }
    public function updatedSelectedMemberIds(): void { $this->buildPreview(); }

    // ─── Member Search ──────────────────────────────────────────────────────────

    public function getSearchResultsProperty(): Collection
    {
        if (strlen($this->memberSearch) < 2) return collect();

        $restaurant = Filament::getTenant();
        return Member::where('restaurant_id', $restaurant->id)
            ->where(function ($q) {
                $q->where('name', 'LIKE', "%{$this->memberSearch}%")
                  ->orWhere('whatsapp', 'LIKE', "%{$this->memberSearch}%")
                  ->orWhere('email', 'LIKE', "%{$this->memberSearch}%");
            })
            ->whereNotIn('id', $this->selectedMemberIds)
            ->limit(10)
            ->get(['id', 'name', 'whatsapp', 'email', 'tier']);
    }

    public function addMember(int $memberId): void
    {
        if (!in_array($memberId, $this->selectedMemberIds)) {
            $this->selectedMemberIds[] = $memberId;
        }
        $this->memberSearch = '';
        $this->buildPreview();
    }

    public function removeMember(int $memberId): void
    {
        $this->selectedMemberIds = array_values(
            array_filter($this->selectedMemberIds, fn($id) => $id !== $memberId)
        );
        $this->buildPreview();
    }

    public function getSelectedMembersProperty(): Collection
    {
        if (empty($this->selectedMemberIds)) return collect();
        return Member::whereIn('id', $this->selectedMemberIds)
            ->get(['id', 'name', 'whatsapp', 'email', 'tier']);
    }

    // ─── Preview ────────────────────────────────────────────────────────────────

    public function buildPreview(): void
    {
        $restaurant = Filament::getTenant();
        $members    = $this->resolveTargetMembers($restaurant->id);

        if ($members->isEmpty()) {
            $this->resetPreview();
            return;
        }

        // Hitung member dengan GC aktif
        $phones   = $members->pluck('whatsapp')->filter()->values();
        $emails   = $members->pluck('email')->filter()->values();

        $activeGcPhones = GiftCard::where('restaurant_id', $restaurant->id)
            ->where('status', 'active')
            ->whereIn('recipient_phone', $phones)
            ->pluck('recipient_phone')
            ->toArray();

        $activeGcEmails = GiftCard::where('restaurant_id', $restaurant->id)
            ->where('status', 'active')
            ->whereIn('recipient_email', $emails)
            ->pluck('recipient_email')
            ->toArray();

        $this->duplicateMembers = $members->filter(function ($m) use ($activeGcPhones, $activeGcEmails) {
            return in_array($m->whatsapp, $activeGcPhones) || in_array($m->email, $activeGcEmails);
        })->values()->toArray();

        $noContact = $members->filter(fn($m) => !$m->whatsapp && !$m->email)->count();
        $totalValue = $this->calculateTotalValue($members);

        $this->previewData = [
            'total'      => $members->count(),
            'duplicates' => count($this->duplicateMembers),
            'no_contact' => $noContact,
            'total_value'=> $totalValue,
        ];
    }

    protected function calculateTotalValue(Collection $members): float
    {
        if ($this->targetType === 'by_tier') {
            return $members->sum(function ($m) {
                return match (strtolower($m->tier ?? 'bronze')) {
                    'gold'   => $this->goldAmount,
                    'silver' => $this->silverAmount,
                    default  => $this->bronzeAmount,
                };
            });
        }

        return $members->count() * $this->flatAmount;
    }

    protected function resetPreview(): void
    {
        $this->previewData      = [];
        $this->duplicateMembers = [];
    }

    // ─── Process ────────────────────────────────────────────────────────────────

    public function submit(): void
    {
        $this->buildPreview();

        // Jika ada duplikat, tampilkan modal konfirmasi
        if (!empty($this->duplicateMembers) && !$this->showConfirmModal) {
            $this->showConfirmModal = true;
            return;
        }

        $this->showConfirmModal = false;
        $this->processDistribution();
    }

    public function confirmAndProcess(bool $skip): void
    {
        $this->skipExisting     = $skip;
        $this->showConfirmModal = false;
        $this->processDistribution();
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
    }

    protected function processDistribution(): void
    {
        $this->validate($this->rules());

        $restaurant = Filament::getTenant();
        $members    = $this->resolveTargetMembers($restaurant->id);

        if ($members->isEmpty()) {
            Notification::make()->warning()->title('Tidak ada member yang ditemukan.')->send();
            return;
        }

        // Handle Single mode langsung
        if ($this->targetType === 'single') {
            $this->processSingle($restaurant);
            return;
        }

        $memberIds = $members->pluck('id')->toArray();
        $config    = $this->buildConfig();

        $this->isProcessing = true;

        if (count($memberIds) < 50) {
            // Proses sync langsung
            $this->processSync($memberIds, $config, $restaurant);
        } else {
            // Dispatch ke queue
            ProcessGiftCardDistribution::dispatch(
                $restaurant->id,
                $memberIds,
                $config,
                auth()->id()
            );

            Notification::make()
                ->info()
                ->title('⏳ Distribusi sedang diproses...')
                ->body('Gift Card untuk ' . count($memberIds) . ' member sedang dibuat di background. Anda akan mendapat notifikasi ketika selesai.')
                ->send();

            $this->resetForm();
        }

        $this->isProcessing = false;
    }

    protected function processSingle($restaurant): void
    {
        $card = GiftCard::create([
            'restaurant_id'    => $restaurant->id,
            'created_by'       => auth()->id(),
            'code'             => GiftCard::generateCode(),
            'recipient_name'   => $this->singleName,
            'recipient_phone'  => $this->singlePhone ?: null,
            'recipient_email'  => $this->singleEmail ?: null,
            'personal_message' => $this->personalMessage ?: null,
            'original_amount'  => $this->singleAmount,
            'remaining_balance'=> $this->singleAmount,
            'status'           => 'active',
            'expires_at'       => $this->expiresAt ?: null,
        ]);

        $result = GiftCardResource::dispatchGiftCardNotifications($card, $restaurant);
        $channels = !empty($result['sent']) ? implode(' & ', $result['sent']) : null;

        Notification::make()
            ->success()
            ->title('Gift Card berhasil dibuat!')
            ->body("Kode {$card->code}" . ($channels ? " · Terkirim via {$channels}" : ''))
            ->send();

        $this->resetForm();
    }

    protected function processSync(array $memberIds, array $config, $restaurant): void
    {
        $success = 0; $skipped = 0; $failed = 0;

        $members = Member::whereIn('id', $memberIds)->get();

        foreach ($members as $member) {
            try {
                if ($config['skip_existing']) {
                    $hasActive = GiftCard::where('restaurant_id', $restaurant->id)
                        ->where('status', 'active')
                        ->where(function ($q) use ($member) {
                            if ($member->whatsapp) $q->orWhere('recipient_phone', $member->whatsapp);
                            if ($member->email)    $q->orWhere('recipient_email', $member->email);
                        })->exists();

                    if ($hasActive) { $skipped++; continue; }
                }

                $amount  = $this->resolveAmountForMember($member, $config);
                if (!$amount) { $skipped++; continue; }

                $message = str_replace('{name}', $member->name, $config['personal_message'] ?? '');

                $card = GiftCard::create([
                    'restaurant_id'    => $restaurant->id,
                    'created_by'       => auth()->id(),
                    'code'             => GiftCard::generateCode(),
                    'recipient_name'   => $member->name,
                    'recipient_phone'  => $member->whatsapp,
                    'recipient_email'  => $member->email,
                    'personal_message' => $message ?: null,
                    'original_amount'  => $amount,
                    'remaining_balance'=> $amount,
                    'status'           => 'active',
                    'expires_at'       => $config['expires_at'] ?? null,
                ]);

                GiftCardResource::dispatchGiftCardNotifications($card, $restaurant);
                $success++;
            } catch (\Exception $e) {
                \Log::error("GiftCard Sync Error [{$member->id}]: " . $e->getMessage());
                $failed++;
            }
        }

        $body = "✅ {$success} berhasil dibuat";
        if ($skipped > 0) $body .= " · ⏭️ {$skipped} dilewati";
        if ($failed  > 0) $body .= " · ❌ {$failed} gagal";

        Notification::make()->success()->title('Distribusi Gift Card Selesai!')->body($body)->send();
        $this->resetForm();
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    protected function resolveTargetMembers(int $restaurantId): Collection
    {
        return match ($this->targetType) {
            'all_members'    => Member::where('restaurant_id', $restaurantId)->get(),
            'by_tier'        => Member::where('restaurant_id', $restaurantId)
                                    ->whereIn('tier', $this->selectedTiers)
                                    ->get(),
            'select_members' => Member::where('restaurant_id', $restaurantId)
                                    ->whereIn('id', $this->selectedMemberIds)
                                    ->get(),
            default          => collect(), // single tidak pakai ini
        };
    }

    protected function resolveAmountForMember(Member $member, array $config): float
    {
        if (isset($config['flat_amount'])) {
            return (float) $config['flat_amount'];
        }
        $tier = strtolower($member->tier ?? 'bronze');
        return (float) ($config['tier_amounts'][$tier] ?? 0);
    }

    protected function buildConfig(): array
    {
        $config = [
            'personal_message' => $this->personalMessage,
            'expires_at'       => $this->expiresAt ?: null,
            'skip_existing'    => $this->skipExisting,
        ];

        if ($this->targetType === 'by_tier') {
            $config['tier_amounts'] = [
                'bronze' => $this->bronzeAmount,
                'silver' => $this->silverAmount,
                'gold'   => $this->goldAmount,
            ];
        } else {
            $config['flat_amount'] = $this->flatAmount;
        }

        return $config;
    }

    protected function rules(): array
    {
        $rules = [
            'expiresAt' => 'nullable|date|after:today',
        ];

        if ($this->targetType === 'single') {
            $rules['singleName']   = 'required|min:2';
            $rules['singleAmount'] = 'required|numeric|min:10000';
        } elseif ($this->targetType === 'by_tier') {
            $rules['selectedTiers'] = 'required|array|min:1';
            // Validasi jumlah per tier yang dipilih
            foreach ($this->selectedTiers as $tier) {
                $rules["{$tier}Amount"] = 'required|numeric|min:10000';
            }
        } else {
            $rules['flatAmount'] = 'required|numeric|min:10000';
        }

        return $rules;
    }

    protected function resetForm(): void
    {
        $this->targetType       = 'single';
        $this->singleName       = '';
        $this->singlePhone      = '';
        $this->singleEmail      = '';
        $this->singleAmount     = 0;
        $this->personalMessage  = '';
        $this->expiresAt        = '';
        $this->flatAmount       = 0;
        $this->selectedTiers    = [];
        $this->bronzeAmount     = 0;
        $this->silverAmount     = 0;
        $this->goldAmount       = 0;
        $this->memberSearch     = '';
        $this->selectedMemberIds = [];
        $this->previewData      = [];
        $this->duplicateMembers = [];
        $this->isProcessing     = false;
    }
}
