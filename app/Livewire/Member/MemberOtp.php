<?php

namespace App\Livewire\Member;

use App\Models\Member;
use App\Models\Restaurant;
use App\Services\MemberOtpService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.member-portal')]
class MemberOtp extends Component
{
    public Restaurant $restaurant;
    public string $otp      = '';
    public int $attempts    = 0;
    public bool $expired    = false;

    public function mount(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;

        // Harus ada pending OTP session
        if (!session("member_otp_pending_{$restaurant->id}")) {
            $this->redirect(route('member.portal.login', $restaurant->slug));
        }

        // Sudah login, redirect
        if (session("member_portal_{$restaurant->id}")) {
            $this->redirect(route('member.portal.dashboard', $restaurant->slug));
        }
    }

    public function verify(): void
    {
        $this->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits'   => 'Kode OTP harus 6 digit angka.',
        ]);

        $memberId = session("member_otp_pending_{$this->restaurant->id}");
        $member   = Member::find($memberId);

        if (!$member) {
            $this->redirect(route('member.portal.login', $this->restaurant->slug));
            return;
        }

        if (MemberOtpService::verify($member, $this->otp)) {
            // Hapus pending OTP session
            session()->forget("member_otp_pending_{$this->restaurant->id}");

            // Buat login session (7 hari)
            session([
                "member_portal_{$this->restaurant->id}" => $member->id,
            ]);
            session()->put("member_portal_{$this->restaurant->id}_expires", now()->addDays(7)->toDateTimeString());

            $this->redirect(route('member.portal.dashboard', $this->restaurant->slug));
        } else {
            // Ambil token terakhir untuk cek sisa percobaan
            $token = \App\Models\MemberOtpToken::where('member_id', $member->id)->latest()->first();
            $sisa  = 3 - ($token->attempts ?? 0);

            if ($sisa <= 0) {
                session()->forget("member_otp_pending_{$this->restaurant->id}");
                $this->expired = true;
            } else {
                $this->addError('otp', "Kode OTP salah atau sudah kadaluarsa. Sisa percobaan: {$sisa}");
            }
        }
    }

    public function backToLogin(): void
    {
        session()->forget("member_otp_pending_{$this->restaurant->id}");
        $this->redirect(route('member.portal.login', $this->restaurant->slug));
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.otp', [
            'restaurant' => $this->restaurant,
        ]);
    }
}
