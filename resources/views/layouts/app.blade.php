<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDescription ?? 'AksesKita memetakan aksesibilitas fasilitas publik di Jakarta & Tangerang — ramp, lift, ubin pemandu, dan toilet disabilitas, dinilai warga untuk warga.' }}">
    <meta name="theme-color" content="#ffffff">

    <title>{{ $title ?? 'AksesKita — Peta Aksesibilitas Jakarta & Tangerang' }}</title>

    {{-- Pre-paint text scale preference to avoid FOUC --}}
    <script>
        (function () {
            try {
                var d = document.documentElement;
                var s = localStorage.getItem('textScale') || localStorage.getItem('ak-text-scale');

                // Bersihkan cache sisa pengaturan kontras & nyaman jika sebelumnya pernah aktif
                localStorage.removeItem('highContrast');
                localStorage.removeItem('ak-contrast');
                localStorage.removeItem('comfort');
                localStorage.removeItem('ak-comfort');

                if (s && ['100', '125', '150'].indexOf(s) !== -1) {
                    d.style.fontSize = s + '%';
                }
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=archivo:500,600,700,800,900|hanken-grotesk:400,500,600,700,800|atkinson-hyperlegible:400,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-white text-slate-900 antialiased">
    {{-- Skip to Content Link (WCAG 2.4.1) — Hanya terlihat saat pertama kali ditekan Tab oleh pengguna Keyboard & Screen Reader --}}
    <a href="#main"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[9999] focus:inline-flex focus:items-center focus:gap-2.5 focus:rounded-xl focus:bg-amber-400 focus:px-6 focus:py-3.5 focus:text-sm focus:font-black focus:text-slate-950 focus:shadow-2xl focus:ring-4 focus:ring-slate-950 focus:outline-none"
       aria-label="Lewati menu navigasi dan langsung menuju konten utama">
        <x-pictogram name="arrow" class="h-4 w-4" />
        <span>Lewati ke Konten Utama (Skip to Content)</span>
    </a>

    <x-navbar />

    {{-- Status toast: announced politely to screen readers --}}
    @if (session('status'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show"
             x-transition.opacity.duration.400ms
             role="status" aria-live="polite"
             class="fixed inset-x-4 top-24 z-[1200] mx-auto max-w-md">
            <div class="flex items-start gap-3 rounded-2xl border-2 border-go-500 bg-white p-4 shadow-2xl shadow-slate-900/20">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-go-600 text-white" aria-hidden="true">
                    <x-pictogram name="check" class="h-5 w-5" />
                </span>
                <p class="pt-1.5 text-sm font-bold text-slate-900">{{ session('status') }}</p>
                <button @click="show = false" class="ml-auto grid h-8 w-8 place-items-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-4 focus-visible:ring-amber-400" aria-label="Tutup notifikasi">&times;</button>
            </div>
        </div>
    @endif

    <main id="main" tabindex="-1" role="main" aria-label="Konten utama">
        @yield('content')
    </main>

    <x-footer />

    {{-- Script helper peta kustom --}}
    <script src="{{ asset('assets/js/leaflet-map.js') }}"></script>
    @stack('scripts')
</body>
</html>
