@php
    $user = auth()->user();
    $tenant = \Filament\Facades\Filament::getTenant();
    if (!$user || !$tenant) return;

    $owner = $tenant->owner;
    $subscription = $owner->activeSubscription;
    
    // Hanya tampilkan jika sedang masa trial
    if (!$subscription || !$subscription->plan->is_trial) return;
    
    $daysLeft = (int) now()->diffInDays($subscription->expires_at, false);
@endphp

<div style="
    background: linear-gradient(135deg, #1a1c1e 0%, #2c3e50 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 24px;
    margin-top: 24px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    position: relative;
    overflow: hidden;
    color: white;
    font-family: 'Inter', sans-serif;
">
    <!-- Accent Light -->
    <div style="position: absolute; top: -50px; left: -50px; width: 150px; height: 150px; background: rgba(243, 156, 18, 0.15); filter: blur(40px); border-radius: 50%;"></div>

    <div style="display: flex; align-items: center; gap: 20px; position: relative; z-index: 2;">
        <div style="
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        ">
            <svg style="width: 32px; height: 32px; color: #f39c12;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
        </div>
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <span style="background: rgba(243, 156, 18, 0.2); color: #f39c12; font-size: 10px; font-weight: 900; padding: 2px 8px; border-radius: 20px; letter-spacing: 1px; text-transform: uppercase;">Masa Trial</span>
                <h3 style="margin: 0; font-size: 18px; font-weight: 800;">{{ $subscription->plan->name }} Aktif</h3>
            </div>
            <p style="margin: 0; color: #a0aec0; font-size: 14px;">
                Tersisa <strong style="color: white; border-bottom: 2px solid #f39c12;">{{ $daysLeft }} hari</strong> masa uji coba. Upgrade sekarang untuk akses selamanya!
            </p>
        </div>
    </div>

    <div style="position: relative; z-index: 2;">
        <a href="{{ \App\Filament\Restaurant\Pages\MySubscription::getUrl(['tenant' => $tenant]) }}" 
           style="
            background: #f39c12;
            color: white;
            padding: 12px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 800;
            font-size: 14px;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
           "
           onmouseover="this.style.background='#e67e22'; this.style.transform='translateY(-2px)';"
           onmouseout="this.style.background='#f39c12'; this.style.transform='translateY(0)';"
        >
            Beli Paket Sekarang
        </a>
    </div>
</div>
