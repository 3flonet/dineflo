<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\ContactSubmission;
use App\Settings\GeneralSettings;
use Livewire\Attributes\On;

class Chatbot extends Component
{
    public $isOpen = false;
    public $step = 0; // 0: Greeting, 1: Asking Name, 2: Asking Phone, 3: Asking Email, 4: Asking Reason, 5: Success/Redirect
    public $messages = [];
    public $userInput = '';
    public $isTyping = false;
    
    // Captured Data
    public $name = '';
    public $phone = '';
    public $email = '';
    public $reason = '';

    public function mount(GeneralSettings $settings)
    {
        if (!$settings->chatbot_active) return;

        // Message 1: Initial Greeting
        $this->messages[] = [
            'id' => uniqid(),
            'type' => 'bot',
            'text' => $settings->chatbot_initial_greeting,
            'time' => now()->format('H:i')
        ];
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        
        // Auto-trigger the first question if just opened and at step 0
        if ($this->isOpen && $this->step == 0) {
            $this->dispatch('bot-thinking');
        }
    }

    public function sendMessage()
    {
        if (empty(trim($this->userInput))) return;

        $input = trim($this->userInput);
        $this->userInput = '';

        // 1. Add User Message Instantly
        $this->messages[] = [
            'id' => uniqid(),
            'type' => 'user',
            'text' => $input,
            'time' => now()->format('H:i')
        ];

        // 2. Set Typing State
        $this->isTyping = true;
        
        // Validation check based on current step
        if ($this->step == 1) {
            if (strlen($input) < 2) {
                $this->dispatch('bot-thinking', text: "Maaf, namanya sepertinya terlalu pendek. Boleh tuliskan nama lengkap Anda? 😊");
                return;
            }
            $this->name = $input;
        }
        elseif ($this->step == 2) {
            if (!preg_match('/^[0-9]{10,15}$/', str_replace(['+', ' ', '-'], '', $input))) {
                $this->dispatch('bot-thinking', text: "Maaf, sepertinya format nomor WhatsApp Anda salah. Mohon masukkan nomor yang valid ya (contoh: 08123456789) 😊");
                return;
            }
            $this->phone = $input;
        } 
        elseif ($this->step == 3) {
            if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                $this->dispatch('bot-thinking', text: "Ups! Format emailnya sepertinya kurang tepat. Mohon masukkan alamat email yang benar ya (contoh: nama@email.com) 😊");
                return;
            }
            $this->email = $input;
        }
        elseif ($this->step == 4) {
            if (strlen($input) < 10) {
                $this->dispatch('bot-thinking', text: "Bisa diceritakan sedikit lebih detail lagi alasannya? Agar kami bisa membantu Anda dengan lebih baik 😊");
                return;
            }
            $this->reason = $input;
        }

        $this->dispatch('bot-thinking');
    }

    #[On('bot-thinking')] 
    public function processBotReply(?string $text = null)
    {
        $settings = app(GeneralSettings::class);
        $this->isTyping = true;

        // Visual delay simulation
        usleep(1500000); // 1.5 seconds delay

        if ($text) {
            $this->addBotMessage($text);
            $this->isTyping = false;
            return;
        }

        // Logic for steps
        if ($this->step == 0) {
            $this->step = 1;
            $this->addBotMessage($settings->chatbot_ask_name_message);
        }
        elseif ($this->step == 1) {
            $this->step = 2;
            $this->addBotMessage("Halo {$this->name}! " . $settings->chatbot_ask_phone_message);
        } 
        elseif ($this->step == 2) {
            $this->step = 3;
            $this->addBotMessage($settings->chatbot_ask_email_message);
        }
        elseif ($this->step == 3) {
            $this->step = 4;
            $this->addBotMessage($settings->chatbot_ask_reason_message);
        }
        elseif ($this->step == 4) {
            $this->step = 5;
            
            // Save to Database
            ContactSubmission::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'subject' => 'Chatbot Consultation Inquiry',
                'message' => $this->reason, // Store the captured reason here
                'status' => 'new'
            ]);

            $this->addBotMessage($settings->chatbot_final_message);

            // Redirect logic
            $waMessage = urlencode("Halo Tim Dineflo, saya {$this->name}.\n\nSaya ingin berkonsultasi mengenai: {$this->reason}");
            $waUrl = "https://wa.me/{$settings->chatbot_whatsapp_number}?text={$waMessage}";
            $this->dispatch('chatbot-redirect', url: $waUrl);
        }

        $this->isTyping = false;
    }

    private function addBotMessage($text)
    {
        $this->messages[] = [
            'id' => uniqid(),
            'type' => 'bot',
            'text' => $text,
            'time' => now()->format('H:i')
        ];
    }

    public function render(GeneralSettings $settings)
    {
        return view('livewire.public.chatbot', [
            'settings' => $settings
        ]);
    }
}
