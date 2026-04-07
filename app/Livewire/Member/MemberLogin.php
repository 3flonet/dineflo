<?php

namespace App\Livewire\Member;

use App\Models\Member;
use App\Models\Restaurant;
use App\Services\MemberOtpService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.member-portal')]
class MemberLogin extends Component
{
    public Restaurant $restaurant;
    public string $whatsapp = '';
    public bool $otpSent    = false;
    public array $sentVia   = [];
    public ?int $memberId   = null;

    public function mount(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;

        // Jika sudah login, redirect ke portal
        if (session("member_portal_{$restaurant->id}")) {
            $this->redirect(route('member.portal.dashboard', $restaurant->slug));
        }
    }

    public function sendOtp(): void
    {
        $this->validate([
            'whatsapp' => 'required|min:9|max:16',
        ], [
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.min'      => 'Nomor tidak valid.',
        ]);

        // Normalize phone
        $normalized = $this->normalizePhone($this->whatsapp);

        // Cari member
        $member = Member::where('restaurant_id', $this->restaurant->id)
            ->where('whatsapp', $normalized)
            ->first();

        if (!$member) {
            $this->addError('whatsapp', 'Nomor ini tidak terdaftar sebagai member di ' . $this->restaurant->name . '.');
            return;
        }

        // Cek minimal 1 channel bisa dikirim
        $waActive   = $this->restaurant->wa_is_active && $this->restaurant->wa_api_key;
        $hasEmail   = !empty($member->email);

        if (!$waActive && !$hasEmail) {
            $this->addError('whatsapp', 'Maaf, login tidak tersedia saat ini. Silakan hubungi ' . $this->restaurant->name . ' langsung.');
            return;
        }

        // Rate Limit OTP Request: Max 1x per minute
        if (!MemberOtpService::canSend($member)) {
            $this->addError('whatsapp', 'Mohon tunggu 1 menit sebelum meminta kode OTP kembali.');
            return;
        }

        // Kirim OTP
        $sentVia = MemberOtpService::send($member, $this->restaurant);

        if (empty($sentVia)) {
            $this->addError('whatsapp', 'Gagal mengirim OTP. Silakan coba lagi.');
            return;
        }

        // Simpan state ke session sementara (bukan login session)
        session(["member_otp_pending_{$this->restaurant->id}" => $member->id]);

        $this->memberId = $member->id;
        $this->sentVia  = $sentVia;
        $this->otpSent  = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.login', [
            'restaurant' => $this->restaurant,
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '08')) {
            return '62' . substr($clean, 1);
        }
        if (str_starts_with($clean, '8') && strlen($clean) >= 9) {
            return '62' . $clean;
        }
        return $clean;
    }
}
