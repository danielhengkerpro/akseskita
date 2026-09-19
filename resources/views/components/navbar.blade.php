@php
    $user = auth()->user();
@endphp

{{--
    Top bar: Clean, accessible white-themed wayfinding navigation.
    Uses flexbox layout that degrades gracefully at large text scales.
    Left: Identity brand.
    Centre: Destination links (hidden below xl when zoomed).
    Right: Zoom control + Auth Actions (Masuk & Daftar).
--}}
<header x-data="{
            open: false,
            scrolled: false,
        }"
        @scroll.window="scrolled = window.scrollY > 20"
        :class="scrolled ? 'shadow-md shadow-slate-900/5 bg-white/98 backdrop-blur-md' : 'bg-white/90 backdrop-blur-md'"
        class="fixed inset-x-0 top-0 z-[1100] w-full border-b border-slate-200/80 transition-all duration-300">
    <nav class="relative mx-auto flex w-full max-w-7xl items-center px-4 py-2.5 sm:px-6 lg:px-8" aria-label="Navigasi utama">
        {{-- Identity plate (Kiri) --}}
        <a href="{{ route('home') }}" class="group flex items-center gap-2 rounded-xl py-1 shrink-0 focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
            <span class="grid h-9 w-9 sm:h-10 sm:w-10 place-items-center rounded-2xl bg-route-cyan text-white shadow-md shadow-route-cyan/25 transition group-hover:scale-105 shrink-0">
                <x-pictogram name="wheelchair" class="h-5 w-5 sm:h-5.5 sm:w-5.5" :stroke="2.2" />
            </span>
            <span class="text-base sm:text-xl font-extrabold tracking-tight text-slate-900 whitespace-nowrap">
                Akses<span class="text-route-cyan">Kita</span>
            </span>
        </a>

        {{-- Spacer pushes center & right apart --}}
        <div class="flex-1"></div>

        {{-- Destination links (flexbox flow, collapses to burger below lg) --}}
        <div class="hidden lg:flex items-center gap-1">
            <a href="{{ route('home') }}" class="flex h-9 items-center gap-1.5 rounded-xl px-3 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                <x-pictogram name="pin" class="h-4 w-4 text-slate-400 shrink-0" /> Beranda
            </a>
            <a href="{{ route('map.index') }}" class="flex h-9 items-center gap-1.5 rounded-xl px-3 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                <x-pictogram name="locate" class="h-4 w-4 text-slate-400 shrink-0" /> Peta Akses
            </a>
            <a href="{{ route('home') }}#cara-kerja" class="flex h-9 items-center gap-1.5 rounded-xl px-3 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                <x-pictogram name="community" class="h-4 w-4 text-slate-400 shrink-0" /> Cara Kerja
            </a>
            <a href="{{ route('home') }}#statistik" class="flex h-9 items-center gap-1.5 rounded-xl px-3 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                <x-pictogram name="ruler" class="h-4 w-4 text-slate-400 shrink-0" /> Data
            </a>
        </div>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Actions (Kanan: Zoom & Auth) --}}
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            {{-- Zoom Control — icon-only on small screens, with label on larger --}}
            <button type="button" @click="$store.a11y.cycleTextScale()"
                    :class="$store.a11y.textScale !== 100 ? 'bg-amber-400 text-slate-950 font-black ring-2 ring-slate-950' : 'text-slate-700 bg-slate-100 hover:bg-slate-200 hover:text-slate-950 font-bold'"
                    class="flex h-9 items-center gap-1.5 rounded-xl px-2.5 text-xs transition whitespace-nowrap shrink-0 focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none cursor-pointer"
                    :title="'Perbesar Tampilan / Zoom: ' + $store.a11y.getTextLabel() + '. Klik untuk mengubah.'"
                    :aria-label="'Ubah zoom tampilan, saat ini: ' + $store.a11y.getTextLabel()">
                <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    <line x1="11" y1="8" x2="11" y2="14"></line>
                    <line x1="8" y1="11" x2="14" y2="11"></line>
                </svg>
                <span class="font-extrabold hidden sm:inline" x-text="$store.a11y.textScale + '%'">100%</span>
            </button>

            @auth
                @if ($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex h-9 items-center gap-1.5 rounded-xl bg-slate-100 px-3 text-sm font-extrabold text-slate-800 transition hover:bg-route-cyan hover:text-white whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M3 3a1 1 0 000 2h14a1 1 0 100-2H3zM3 8a1 1 0 100 2h9a1 1 0 100-2H3zM3 13a1 1 0 100 2h5a1 1 0 100-2H3z"/></svg>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="flex h-9 items-center gap-1.5 rounded-xl bg-slate-100 px-3 text-sm font-extrabold text-slate-800 transition hover:bg-route-cyan hover:text-white whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('user.report.create') }}" class="flex h-9 items-center gap-1.5 rounded-xl bg-go-600 px-3 text-sm font-extrabold text-white shadow-md shadow-go-600/20 transition hover:bg-go-700 whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                        <x-pictogram name="report" class="h-3.5 w-3.5 shrink-0" />
                        <span>Lapor</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="flex h-9 items-center rounded-xl px-2.5 text-sm font-bold text-slate-500 transition hover:bg-stop-50 hover:text-stop-600 whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex h-9 items-center rounded-xl px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">Masuk</a>
                <a href="{{ route('register') }}" class="flex h-9 items-center justify-center rounded-xl bg-route-cyan px-4 text-sm font-extrabold text-white shadow-md shadow-route-cyan/20 transition hover:bg-route-cyan-ink whitespace-nowrap focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                    Daftar
                </a>
            @endauth

            {{-- Mobile/tablet menu button (visible below xl) --}}
            <button @click="open = !open" type="button"
                    class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none lg:hidden shrink-0"
                    :aria-expanded="open" aria-controls="menu-mobile" aria-label="Buka menu navigasi">
                <svg x-show="!open" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 5A.75.75 0 012.75 9h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 9.75zm0 5a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75z" clip-rule="evenodd"/></svg>
                <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
            </button>
        </div>
    </nav>

    {{-- Mobile/tablet drawer (visible below xl) --}}
    <div x-show="open" x-cloak x-transition.origin.top id="menu-mobile"
         class="block lg:hidden border-t border-slate-200 bg-white">
        <div class="space-y-1 px-4 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 focus-visible:ring-4 focus-visible:ring-amber-400">
                <x-pictogram name="pin" class="h-5 w-5 text-route-cyan" /> Beranda
            </a>
            <a href="{{ route('map.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 focus-visible:ring-4 focus-visible:ring-amber-400">
                <x-pictogram name="locate" class="h-5 w-5 text-route-cyan" /> Peta Akses
            </a>
            <a href="{{ route('home') }}#cara-kerja" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 focus-visible:ring-4 focus-visible:ring-amber-400">
                <x-pictogram name="community" class="h-5 w-5 text-route-cyan" /> Cara Kerja
            </a>
            <a href="{{ route('home') }}#statistik" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 focus-visible:ring-4 focus-visible:ring-amber-400">
                <x-pictogram name="ruler" class="h-5 w-5 text-route-cyan" /> Data
            </a>

            <div class="my-3 border-t border-slate-200"></div>

            {{-- Mobile Zoom Control --}}
            <div class="px-1 pb-2">
                <button type="button" @click="$store.a11y.cycleTextScale()"
                        :class="$store.a11y.textScale !== 100 ? 'bg-amber-400 text-slate-950 font-black ring-2 ring-slate-950' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold'"
                        class="flex h-10 w-full items-center justify-center gap-2 rounded-xl text-xs transition focus-visible:ring-4 focus-visible:ring-amber-400"
                        :aria-label="'Ubah zoom tampilan, saat ini: ' + $store.a11y.getTextLabel()">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="11" y1="8" x2="11" y2="14"></line>
                        <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                    <span class="font-extrabold" x-text="$store.a11y.getTextLabel()">Zoom 100%</span>
                </button>
            </div>

            @auth
                @if ($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-4 py-2.5 text-sm font-bold text-route-cyan-ink hover:bg-slate-50">Dashboard Admin</a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="block rounded-xl px-4 py-2.5 text-sm font-bold text-route-cyan-ink hover:bg-slate-50">Dashboard</a>
                    <a href="{{ route('user.report.create') }}" class="flex items-center justify-center gap-2 rounded-xl bg-go-600 px-4 py-2.5 text-center text-sm font-extrabold text-white">
                        <x-pictogram name="report" class="h-4 w-4" /> Buat Laporan
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mt-1 block w-full rounded-xl px-4 py-2.5 text-left text-sm font-bold text-stop-600 hover:bg-stop-50">Keluar</button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('login') }}" class="flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 hover:bg-slate-100">Masuk</a>
                    <a href="{{ route('register') }}" class="flex h-11 items-center justify-center rounded-xl bg-route-cyan text-sm font-extrabold text-white shadow-xs hover:bg-route-cyan-ink">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</header>
