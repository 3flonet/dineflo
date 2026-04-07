<?php

namespace App\Livewire\Public;

use Livewire\Component;

use App\Models\ContactSubmission;
use App\Settings\GeneralSettings;

class ConsultationPage extends Component
{
    public $name;
    public $email;
    public $phone;
    public $subject = 'Konsultasi Layanan';
    public $message;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'phone' => 'nullable|numeric',
        'subject' => 'required',
        'message' => 'required|min:10',
    ];

    public function submit()
    {
        $this->validate();

        ContactSubmission::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => 'pending',
        ]);

        session()->flash('message', 'Pesan Anda telah berhasil dikirim. Tim kami akan segera menghubungi Anda.');

        $this->reset(['name', 'email', 'phone', 'message']);
    }

    public function render(GeneralSettings $settings)
    {
        return view('livewire.public.consultation-page', [
            'settings' => $settings,
        ])->layout('components.layouts.app');
    }
}
