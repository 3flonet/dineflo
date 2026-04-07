<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Portal Member — {{ $restaurant->name ?? 'Dineflo' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: {
                        'float-slow': 'float 8s ease-in-out infinite',
                        'float-medium': 'float 6s ease-in-out infinite 1s',
                        'float-fast': 'float 5s ease-in-out infinite 2s',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px) scale(1)' },
                            '50%': { transform: 'translateY(-20px) scale(1.02)' },
                        }
                    }
                }
            }
        }
    </script>
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }
        .input-dark {
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(255,255,255,0.10);
            color: white;
            transition: border-color 0.2s, background 0.2s;
        }
        .input-dark::placeholder { color: rgba(255,255,255,0.25); }
        .input-dark:focus {
            outline: none;
            border-color: rgba(139,92,246,0.7);
            background: rgba(255,255,255,0.09);
        }
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            transition: all 0.2s;
            box-shadow: 0 8px 32px rgba(124,58,237,0.25);
        }
        .btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); box-shadow: 0 12px 40px rgba(124,58,237,0.35); }
        .btn-primary:active { transform: translateY(0); }
        .badge-sent { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); }
    </style>
</head>
<body class="min-h-screen bg-[#080810] text-white overflow-x-hidden">

    {{-- Animated Background Blobs --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden" style="z-index:0">
        <div class="absolute animate-float-slow" style="width:500px;height:500px;top:-100px;left:-120px;background:radial-gradient(circle,rgba(124,58,237,.18) 0%,transparent 70%);border-radius:50%"></div>
        <div class="absolute animate-float-medium" style="width:400px;height:400px;top:50%;right:-100px;background:radial-gradient(circle,rgba(245,158,11,.08) 0%,transparent 70%);border-radius:50%"></div>
        <div class="absolute animate-float-fast" style="width:350px;height:350px;bottom:-50px;left:40%;background:radial-gradient(circle,rgba(79,70,229,.12) 0%,transparent 70%);border-radius:50%"></div>
        {{-- Subtle grid --}}
        <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.015) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.015) 1px,transparent 1px);background-size:48px 48px;"></div>
    </div>

    <div class="relative min-h-screen flex flex-col" style="z-index:1">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
