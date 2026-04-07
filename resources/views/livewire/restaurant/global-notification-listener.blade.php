<div
    x-data="{
        connected: false,
        init() {
            let attempts = 0;
            const checkEcho = setInterval(() => {
                attempts++;
                if (typeof window.Echo !== 'undefined') {
                    clearInterval(checkEcho);
                    console.log('✅ Echo detected. Initializing listeners...');
                    this.setupListeners();
                } else if (attempts > 20) {
                    clearInterval(checkEcho);
                    console.error('❌ Echo failed to load.');
                }
            }, 500);
        },

        setupListeners() {
            const restaurantId = @js($restaurantId);
            const hasWaiterCall = @js($hasWaiterCallFeature);
            const prefs = @js($userPreferences);

            if (!restaurantId) {
                console.error('❌ Restaurant ID not found in component.');
                return;
            }

            console.log('📡 Subscribing to channel: restaurant.' + restaurantId);

            // Listen for New Orders
            window.Echo.private(`orders.${restaurantId}`)
                .subscribed(() => {
                    console.log('✅ Real-time notifications active (Orders)');
                    this.connected = true;
                })
                .listen('OrderCreated', (e) => {
                    console.log('📦 New Order:', e);
                    
                    const eventPrefs = prefs['order_new'] || [];
                    
                    // Play Sound if enabled
                    if (eventPrefs.includes('sound')) {
                        this.playNotificationSound(`New order from ${e.customer_name}`);
                    }
                    
                    this.lastEvent = 'New Order: ' + e.customer_name;
                    $wire.handleOrderCreated(e);
                });

            // Listen for Waiter Calls
            if (hasWaiterCall) {
                window.Echo.private(`restaurant.${restaurantId}`)
                    .subscribed(() => console.log('✅ Real-time notifications active (Waiter Calls)'))
                    .listen('.waiter.called', (e) => {
                        console.log('🔔 Waiter Call:', e);
                        
                        const eventPrefs = prefs['waiter_call'] || [];
                        
                        // Play Sound if enabled
                        if (eventPrefs.includes('sound')) {
                            this.playAlertSound(`Assistance requested at table ${e.table_name}`);
                        }
                        
                        this.lastEvent = 'Waiter Call: Table ' + e.table_name;
                        $wire.handleWaiterCalled(e);
                    });
            }
        },

        playNotificationSound(text) {
            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            audio.play().catch(e => {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.speak(new SpeechSynthesisUtterance(text));
                }
            });
        },

        playAlertSound(text) {
            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/951/951-preview.mp3');
            audio.play().catch(e => {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.speak(new SpeechSynthesisUtterance(text));
                }
            });
        }
    }"
>
    {{-- Subtle Sound Status Indicator (Bottom Right) --}}
    <div 
        x-data="{ primed: false }"
        style="position: fixed; bottom: 15px; right: 15px; z-index: 50; display: flex; align-items: center; gap: 8px; pointer-events: none;"
    >
        <button 
            @click="playNotificationSound('System Ready'); primed = true"
            x-show="!primed"
            style="pointer-events: auto; background: white; color: #6b7280; border: 1px solid #e5e7eb; padding: 6px 12px; border-radius: 999px; font-size: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: all 0.2s;"
            onmouseover="this.style.color='#ea580c'; this.style.borderColor='#ea580c'"
            onmouseout="this.style.color='#6b7280'; this.style.borderColor='#e5e7eb'"
        >
            <div :style="'width: 8px; height: 8px; border-radius: 50%; background: ' + (connected ? '#22c55e' : '#ef4444')"></div>
            ACTIVATE AUDIO
        </button>

        <div 
            x-show="primed"
            style="background: white; border: 1px solid #e5e7eb; padding: 5px; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.05); opacity: 0.5; display: flex; align-items: center; justify-content: center;"
            title="Notification sound is active"
        >
            <svg xmlns="http://www.w3.org/2000/svg" style="height: 14px; width: 14px; color: #22c55e;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
            </svg>
        </div>
    </div>
</div>
