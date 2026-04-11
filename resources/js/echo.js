import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: window.DinefloConfig?.broadcasting?.driver || import.meta.env.VITE_BROADCAST_CONNECTION || 'reverb',
    key: (window.DinefloConfig?.broadcasting?.driver === 'pusher' ? window.DinefloConfig.broadcasting.pusher.key : window.DinefloConfig?.broadcasting?.reverb?.key) 
         || import.meta.env.VITE_REVERB_APP_KEY 
         || import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: window.DinefloConfig?.broadcasting?.pusher?.cluster || import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    wsHost: (window.DinefloConfig?.broadcasting?.driver === 'pusher' 
            ? `ws-${window.DinefloConfig.broadcasting.pusher.cluster}.pusher.com` 
            : (window.DinefloConfig?.broadcasting?.reverb?.host || import.meta.env.VITE_REVERB_HOST)),
    wsPort: (window.DinefloConfig?.broadcasting?.driver === 'pusher' ? 80 : (window.DinefloConfig?.broadcasting?.reverb?.port || import.meta.env.VITE_REVERB_PORT || 80)),
    wssPort: (window.DinefloConfig?.broadcasting?.driver === 'pusher' ? 443 : (window.DinefloConfig?.broadcasting?.reverb?.port || import.meta.env.VITE_REVERB_PORT || 443)),
    forceTLS: (window.DinefloConfig?.broadcasting?.driver === 'pusher' 
              ? true 
              : ((window.DinefloConfig?.broadcasting?.reverb?.scheme || import.meta.env.VITE_REVERB_SCHEME || 'https') === 'https')),
    enabledTransports: ['ws', 'wss'],
});
