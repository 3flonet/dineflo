<script>
    window.DinefloConfig = {
        broadcasting: {
            driver: "{{ $settings->broadcast_driver }}",
            pusher: {
                key: "{{ $settings->pusher_app_key }}",
                cluster: "{{ $settings->pusher_app_cluster }}"
            },
            reverb: {
                key: "{{ $settings->reverb_app_key }}",
                host: "{{ $settings->reverb_host }}",
                port: {{ $settings->reverb_port ?? 8081 }},
                scheme: "{{ $settings->reverb_scheme ?? 'http' }}"
            }
        }
    };
</script>
