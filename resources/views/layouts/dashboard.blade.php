<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard — AksesKita' }}</title>

    {{-- Pre-paint text scale preference to avoid FOUC --}}
    <script>
        (function () {
            try {
                var d = document.documentElement;
                var s = localStorage.getItem('textScale') || localStorage.getItem('ak-text-scale');
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
<body class="min-h-screen bg-paper" x-data="{ sidebar: false }">
    @php $user = auth()->user(); @endphp

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="board-ground fixed inset-y-0 left-0 z-[1150] w-72 -translate-x-full text-white transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
               :class="sidebar ? 'translate-x-0' : ''">
            <div class="flex h-full flex-col">
                <div class="flex items-center gap-2.5 px-6 py-5">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-route-cyan text-board-950">
                        <x-pictogram name="wheelchair" class="h-6 w-6" :stroke="2.2" />
                    </span>
                    <a href="{{ route('home') }}" class="font-display text-lg font-extrabold tracking-tight text-white">Akses<span class="text-route-cyan-soft">Kita</span></a>
                </div>

                {{-- User profile card with account type badge --}}
                <div class="mx-4 rounded-2xl border border-white/10 bg-white/[0.05] p-4">
                    <p class="text-xs font-extrabold uppercase tracking-wide text-route-cyan-soft">
                        {{ $user->getAccountTypeLabel() }}
                    </p>
                    <p class="mt-1 truncate text-sm font-bold text-white">{{ $user->name }}</p>
                    <p class="truncate text-xs font-semibold text-white/50">{{ $user->email }}</p>

                    @if (!$user->isAdmin() && $user->isDisabilityAccount() && $user->getDisabilityInfo())
                        <div class="mt-2.5 flex items-center gap-2 rounded-lg bg-white/10 px-2.5 py-1.5">
                            <span class="text-sm">{{ $user->getDisabilityInfo()['badge'] ?? '♿' }}</span>
                            <span class="text-[10px] font-bold text-white/80">{{ $user->getDisabilityInfo()['label'] ?? 'Disabilitas' }}</span>
                        </div>
                    @endif
                </div>

                <nav class="mt-6 flex-1 space-y-1 px-3">
                    @if ($user->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition {{ request()->routeIs('admin.dashboard') ? 'bg-route-cyan text-board-950 shadow-lg shadow-route-cyan/25' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M3 3a1 1 0 000 2h14a1 1 0 100-2H3zM3 8a1 1 0 100 2h9a1 1 0 100-2H3zM3 13a1 1 0 100 2h5a1 1 0 100-2H3z"/></svg>
                            Ringkasan
                        </a>
                        <a href="{{ route('admin.moderation.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition {{ request()->routeIs('admin.moderation.*') ? 'bg-route-cyan text-board-950 shadow-lg shadow-route-cyan/25' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            Moderasi Laporan
                        </a>
                        <a href="{{ route('admin.places.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition {{ request()->routeIs('admin.places.*') ? 'bg-route-cyan text-board-950 shadow-lg shadow-route-cyan/25' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd"/></svg>
                            Kelola Tempat
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition {{ request()->routeIs('user.dashboard') ? 'bg-route-cyan text-board-950 shadow-lg shadow-route-cyan/25' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                            Beranda Saya
                        </a>
                        <a href="{{ route('user.report.create') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition {{ request()->routeIs('user.report.*') ? 'bg-route-cyan text-board-950 shadow-lg shadow-route-cyan/25' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                            Buat Laporan Baru
                        </a>
                        <a href="{{ route('user.places.create') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition {{ request()->routeIs('user.places.*') ? 'bg-route-cyan text-board-950 shadow-lg shadow-route-cyan/25' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd"/></svg>
                            Ajukan Tempat Baru
                        </a>
                    @endif

                    <a href="{{ route('map.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-white/70 transition hover:bg-white/10 hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.157 2.176a1.5 1.5 0 011.38-.176l3.927 1.59 3.869-1.29a1.5 1.5 0 012.167 1.342v11.716a1.5 1.5 0 01-.967 1.401l-4.313 1.438a1.5 1.5 0 01-1.057-.05L9.31 16.64l-3.843 1.28A1.5 1.5 0 013.3 16.58V4.864a1.5 1.5 0 01.967-1.401l3.89-1.287z" clip-rule="evenodd"/></svg>
                        Peta Publik
                    </a>
                </nav>

                <div class="border-t border-ink-900/5 p-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-stop-300 transition hover:bg-stop-500/15">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 015.25 2h5.5A2.25 2.25 0 0113 4.25v2a.75.75 0 01-1.5 0v-2a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75v-2a.75.75 0 011.5 0v2A2.25 2.25 0 0110.75 18h-5.5A2.25 2.25 0 013 15.75V4.25z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M19 10a.75.75 0 00-.75-.75H8.704l1.048-.943a.75.75 0 10-1.004-1.114l-2.5 2.25a.75.75 0 000 1.114l2.5 2.25a.75.75 0 101.004-1.114l-1.048-.943h9.546A.75.75 0 0019 10z" clip-rule="evenodd"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Overlay mobile --}}
        <div x-show="sidebar" x-cloak @click="sidebar = false" x-transition.opacity
             class="fixed inset-0 z-[1140] bg-ink-900/40 lg:hidden"></div>

        {{-- Main --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-[1130] flex items-center gap-3 border-b-2 border-ink-900/8 bg-white/90 px-4 py-3 backdrop-blur lg:px-8">
                <button @click="sidebar = true" class="grid h-11 w-11 place-items-center rounded-xl border border-ink-900/10 lg:hidden" aria-label="Buka menu">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 5A.75.75 0 012.75 9h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 9.75zm0 5a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75z" clip-rule="evenodd"/></svg>
                </button>
                <div class="min-w-0">
                    <h1 class="truncate font-display text-lg font-extrabold tracking-tight text-ink-900">{{ $heading ?? 'Dashboard' }}</h1>
                    @isset($subheading)
                        <p class="truncate text-sm font-semibold text-ink-500">{{ $subheading }}</p>
                    @endisset
                </div>

                <div class="ml-auto flex items-center gap-2">
                    {{-- Zoom Control (Sama seperti di Home Navbar) --}}
                    <button type="button" @click="$store.a11y.cycleTextScale()"
                            :class="$store.a11y.textScale !== 100 ? 'bg-amber-400 text-slate-950 font-black ring-2 ring-slate-950' : 'text-slate-700 bg-slate-100 hover:bg-slate-200 hover:text-slate-950 font-bold border border-slate-200'"
                            class="flex h-11 items-center gap-1.5 rounded-xl px-3 text-xs sm:text-sm transition whitespace-nowrap shrink-0 focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none cursor-pointer"
                            :title="'Perbesar Tampilan / Zoom: ' + $store.a11y.getTextLabel() + '. Klik untuk mengubah.'"
                            :aria-label="'Ubah zoom tampilan, saat ini: ' + $store.a11y.getTextLabel()">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            <line x1="11" y1="8" x2="11" y2="14"></line>
                            <line x1="8" y1="11" x2="14" y2="11"></line>
                        </svg>
                        <span class="font-extrabold hidden sm:inline" x-text="$store.a11y.textScale + '%'">100%</span>
                    </button>

                    <a href="{{ route('home') }}" class="hidden h-11 items-center gap-2 rounded-xl border-2 border-ink-900/10 px-4 text-sm font-bold text-ink-700 transition hover:border-route-cyan hover:text-route-cyan-ink sm:inline-flex">
                        <x-pictogram name="arrow" class="h-4 w-4 rotate-180" /> Ke Situs
                    </a>
                    <span class="grid h-11 w-11 place-items-center rounded-full bg-board-900 font-display text-sm font-extrabold text-white">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                </div>
            </header>

            @if (session('status'))
                <div class="px-4 pt-4 lg:px-8">
                    <div class="rounded-2xl border-2 border-go-500/30 bg-go-50 px-4 py-3 text-sm font-bold text-go-700">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            <div class="flex-1 px-4 py-6 lg:px-8 lg:py-8">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/leaflet-map.js') }}"></script>
    @stack('scripts')
</body>
</html>
