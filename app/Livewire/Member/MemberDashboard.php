<?php

namespace App\Livewire\Member;

use App\Models\Member;
use App\Models\Restaurant;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.member-portal')]
class MemberDashboard extends Component
{
    public Restaurant $restaurant;
    public Member $member;

    public function mount(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
        $this->member     = $this->authenticatedMember();
    }

    public function logout(): void
    {
        session()->forget("member_portal_{$this->restaurant->id}");
        session()->forget("member_portal_{$this->restaurant->id}_expires");
        $this->redirect(route('member.portal.login', $this->restaurant->slug));
    }

    public function render(): \Illuminate\View\View
    {
        $restaurant = $this->restaurant;
        $member     = $this->member->fresh(); // pastikan data terbaru

        // Hitung progres ke tier berikutnya
        $tierProgress = $this->calcTierProgress($member, $restaurant);

        // 10 transaksi terakhir
        $recentOrders = $member->orders()
            ->with('items.menuItem')
            ->where('payment_status', 'paid')
            ->latest()
            ->take(10)
            ->get();

        // Active Gift Cards untuk member ini (Berdasarkan nomor HP / Email)
        $giftCards = \App\Models\GiftCard::where('restaurant_id', $restaurant->id)
            ->where('status', 'active')
            ->where(function ($query) use ($member) {
                if ($member->whatsapp) {
                    // Cari exact match atau yang nomor belakangnya sama (jika format agak beda)
                    $query->orWhere('recipient_phone', 'LIKE', '%' . substr($member->whatsapp, -8));
                }
                if ($member->email) {
                    $query->orWhere('recipient_email', $member->email);
                }
            })
            ->latest()
            ->get();

        return view('livewire.member.dashboard', compact('member', 'restaurant', 'tierProgress', 'recentOrders', 'giftCards'));
    }

    private function authenticatedMember(): Member
    {
        $memberId = session("member_portal_{$this->restaurant->id}");
        $member   = Member::where('id', $memberId)
            ->where('restaurant_id', $this->restaurant->id)
            ->first();

        if (!$member) {
            session()->forget("member_portal_{$this->restaurant->id}");
            redirect(route('member.portal.login', $this->restaurant->slug))->send();
        }

        return $member;
    }

    private function calcTierProgress(Member $member, Restaurant $restaurant): array
    {
        $spent = (float) $member->total_spent;

        return match ($member->tier) {
            'bronze' => [
                'current'    => 'Bronze',
                'next'       => 'Silver',
                'spent'      => $spent,
                'target'     => (float) $restaurant->loyalty_silver_threshold,
                'percentage' => $restaurant->loyalty_silver_threshold > 0
                    ? min(100, round($spent / $restaurant->loyalty_silver_threshold * 100))
                    : 100,
                'remaining'  => max(0, $restaurant->loyalty_silver_threshold - $spent),
                'color'      => '#cd7f32',
                'nextColor'  => '#c0c0c0',
            ],
            'silver' => [
                'current'    => 'Silver',
                'next'       => 'Gold',
                'spent'      => $spent,
                'target'     => (float) $restaurant->loyalty_gold_threshold,
                'percentage' => $restaurant->loyalty_gold_threshold > 0
                    ? min(100, round($spent / $restaurant->loyalty_gold_threshold * 100))
                    : 100,
                'remaining'  => max(0, $restaurant->loyalty_gold_threshold - $spent),
                'color'      => '#c0c0c0',
                'nextColor'  => '#FFD700',
            ],
            'gold' => [
                'current'    => 'Gold',
                'next'       => null,
                'spent'      => $spent,
                'target'     => null,
                'percentage' => 100,
                'remaining'  => 0,
                'color'      => '#FFD700',
                'nextColor'  => null,
            ],
            default => ['current' => 'Bronze', 'next' => null, 'percentage' => 0, 'remaining' => 0],
        };
    }
}
