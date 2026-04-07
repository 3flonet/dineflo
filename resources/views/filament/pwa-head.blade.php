<link rel="manifest" href="{{ route('pwa.manifest') }}">
<meta name="theme-color" content="#F59E0B">
<link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
             navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(registration => {
                    console.log('SW Registered!', registration);
                })
                .catch(error => {
                    console.error('SW Registration Failed!', error);
                });
        });
    }
</script>
