<x-filament-panels::page>
    {{ $this->table }}

    {{-- Sound notification --}}
    <audio id="notification-sound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUKfk77RgGwU7k9jzxnMpBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBSh+zPLaizsKGGS56+mnUxELTKXh8bllHAU2jdXzzn0vBQ==" type="audio/wav">
    </audio>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('play-notification-sound', () => {
                const audio = document.getElementById('notification-sound');
                if (audio) {
                    audio.play().catch(e => console.log('Audio play failed:', e));
                }
            });
        });
    </script>
</x-filament-panels::page>
