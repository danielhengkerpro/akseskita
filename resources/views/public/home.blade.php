@extends('layouts.app')

@section('title', 'AksesKita — Peta Aksesibilitas Jakarta & Tangerang')
@section('metaDescription', 'Cek dulu apakah stasiun, mal, rumah sakit, atau taman punya ramp, lift, ubin pemandu, dan toilet disabilitas. Peta aksesibilitas dari warga untuk warga di Jakarta & Tangerang.')

@php
    $totalPlaces = $stats['places'] ?? 0;
    $avgScore = $totalPlaces > 0 ? (int) round(\App\Models\Place::avg('overall_score')) : 0;
    $g = max($stats['green'] ?? 0, 0);
    $y = max($stats['yellow'] ?? 0, 0);
    $r = max($stats['red'] ?? 0, 0);
    $sum = max($g + $y + $r, 1);
@endphp

@section('content')

{{-- ============================================================
     HERO — COMPACT & BALANCED WAYFINDING CANVAS
     No dead whitespace; immediate visibility on load.
============================================================ --}}
<section x-data="heroBoard(@js($mapPlaces), @js($mapCenter))"
         class="relative isolate overflow-hidden bg-white pt-24 pb-10 sm:pt-28 lg:pt-28 lg:pb-12">

    <div class="mx-auto grid max-w-7xl items-start gap-8 px-4 sm:px-6 lg:grid-cols-[1fr_1fr] lg:gap-10 lg:px-8">
        {{-- ---------- Left Column: Value Proposition & Affordances ---------- --}}
        <div class="flex flex-col justify-center">
            {{-- Live status indicator bar --}}
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-800">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-go-500 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-go-600"></span>
                    </span>
                    Data Hidup Terverifikasi
                </span>
                <span class="text-xs font-semibold text-slate-500">{{ $totalPlaces }} fasilitas terdata di Jakarta &amp; Tangerang</span>
            </div>

            {{-- Main accessible heading (always visible, no hidden delays) --}}
            <h1 class="sr-only">AksesKita — kemana pun kamu pergi, harus bisa diakses semua.</h1>
            <div class="mt-4 font-display text-3xl font-extrabold leading-[1.18] tracking-tight text-slate-950 sm:text-4xl lg:text-[2.65rem] xl:text-[3.1rem]" aria-hidden="true">
                <span class="block text-slate-900">Kemana pun kamu pergi,</span>
                <div class="mt-1 block text-route-cyan-ink">
                    <span class="relative inline-block">
                        <span x-text="typed">perjalananmu lancar.</span><span class="typing-bar inline-block"></span>
                        <span class="absolute inset-x-0 -bottom-1 h-1.5 rounded-full bg-warn-400"></span>
                    </span>
                </div>
                <span class="mt-1 block text-slate-900">harus bisa diakses semua.</span>
            </div>

            <p class="mt-4 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">
                Cek kepastian sebelum berangkat: apakah stasiun, mal, atau rumah sakit tujuanmu memiliki
                ramp, lift, ubin pemandu, dan toilet disabilitas? AksesKita memetakan enam fasilitas kunci
                dengan skor kejujuran dari observasi warga, diverifikasi manusia.
            </p>

            {{-- Primary Action Buttons --}}
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ route('map.index') }}"
                   class="group flex h-12 items-center gap-2 rounded-xl bg-route-cyan px-6 text-sm font-extrabold text-white shadow-md shadow-route-cyan/20 transition hover:bg-route-cyan-ink focus-visible:outline-route-cyan">
                    <x-pictogram name="locate" class="h-4.5 w-4.5" :stroke="2.4" />
                    Buka Peta Akses
                    <x-pictogram name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" :stroke="2.4" />
                </a>
                <a href="{{ route('register') }}"
                   class="flex h-12 items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-sm font-extrabold text-slate-800 transition hover:border-slate-800 hover:bg-slate-50 focus-visible:outline-slate-800">
                    <x-pictogram name="community" class="h-4.5 w-4.5 text-slate-600" />
                    Daftar Akun Gratis
                </a>
            </div>

            {{-- Simulation mode / Perspective switcher --}}
            <div class="mt-6 rounded-2xl border border-slate-200/90 bg-slate-50/90 p-4">
                <p class="mb-2.5 flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-widest text-slate-500">
                    <x-pictogram name="vision" class="h-3.5 w-3.5 text-route-cyan" /> Simulasikan Sudut Pandang Pengguna
                </p>
                <div class="flex flex-wrap gap-1.5" role="group" aria-label="Mode sudut pandang aksesibilitas">
                    <template x-for="(m, key) in $store.accessMode.modes" :key="key">
                        <button type="button"
                                @click="$store.accessMode.set(key)"
                                :aria-pressed="$store.accessMode.mode === key"
                                class="flex h-9 items-center gap-1.5 rounded-lg px-3 text-xs font-extrabold transition"
                                :class="$store.accessMode.mode === key
                                    ? 'bg-route-violet text-white shadow-xs'
                                    : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900'">
                            <x-pictogram name="info" class="h-3.5 w-3.5" x-bind:class="$store.accessMode.mode === key ? 'opacity-100' : 'opacity-60'" />
                            <span x-text="m.label"></span>
                        </button>
                    </template>
                </div>
                <p class="mt-2 text-xs text-slate-600" aria-live="polite">
                    <span class="font-bold text-slate-900" x-text="$store.accessMode.modes[$store.accessMode.mode].label + ':'"></span>
                    <span x-text="$store.accessMode.modes[$store.accessMode.mode].desc"></span>
                </p>
            </div>
        </div>

        {{-- ---------- Right Column: Elevated Interactive Map Board ---------- --}}
        <div class="relative">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
                {{-- Board header with View Toggle --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-route-cyan text-white shadow-xs">
                            <x-pictogram name="locate" class="h-4.5 w-4.5" :stroke="2.2" />
                        </span>
                        <div>
                            <p class="font-display text-xs font-extrabold uppercase tracking-wide text-slate-900">Peta Akses Jakarta &amp; Tangerang</p>
                            <p class="text-xs font-medium text-slate-500">Pilihan tampilan visual peta atau daftar teks semantik</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Toggle View (Peta vs Daftar Teks) --}}
                        <div class="inline-flex rounded-xl border border-slate-200 bg-white p-0.5 shadow-2xs" role="tablist" aria-label="Pilihan format tampilan">
                            <button type="button" @click="setViewMode('map')"
                                    role="tab" :aria-selected="viewMode === 'map'"
                                    :class="viewMode === 'map' ? 'bg-route-cyan text-white font-black shadow-xs' : 'text-slate-600 hover:text-slate-900 font-bold'"
                                    class="flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs transition focus-visible:ring-4 focus-visible:ring-amber-400"
                                    aria-label="Tampilkan kanvas peta interaktif">
                                <span>🗺️ Peta</span>
                            </button>
                            <button type="button" @click="setViewMode('list')"
                                    role="tab" :aria-selected="viewMode === 'list'"
                                    :class="viewMode === 'list' ? 'bg-route-cyan text-white font-black shadow-xs' : 'text-slate-600 hover:text-slate-900 font-bold'"
                                    class="flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs transition focus-visible:ring-4 focus-visible:ring-amber-400"
                                    aria-label="Tampilkan daftar tempat dalam format teks semantik untuk Screen Reader">
                                <span>📋 Daftar Teks</span>
                            </button>
                        </div>

                        <span class="hidden sm:flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[11px] font-extrabold text-emerald-800">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-signal"></span>
                            <span>{{ $totalPlaces }} tempat</span>
                        </span>
                    </div>
                </div>

                {{-- VIEW 1: Interactive Leaflet Container --}}
                <div x-show="viewMode === 'map'" id="hero-map" class="h-[320px] w-full sm:h-[350px]" wire:ignore
                     tabindex="0" role="application" aria-label="Kanvas peta interaktif hero AksesKita. Gunakan tombol Daftar Teks jika menggunakan pembaca layar."></div>

                {{-- VIEW 2: Semantic HTML Text List for Screen Readers & Keyboard Users --}}
                <section x-show="viewMode === 'list'" x-cloak
                         role="region" aria-label="Daftar teks tempat ramah aksesibilitas"
                         class="h-[320px] w-full overflow-y-auto bg-slate-50 p-4 sm:h-[350px]">
                    <div class="space-y-3">
                        <template x-for="p in places" :key="p.id">
                            <article class="rounded-xl border-2 border-slate-200 bg-white p-3.5 shadow-xs transition hover:border-route-cyan">
                                <header class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="text-[11px] font-black uppercase tracking-wider text-route-cyan-ink" x-text="p.category_label"></span>
                                        <h3 class="font-display text-sm font-extrabold text-slate-950">
                                            <a :href="'/tempat/' + p.slug" class="hover:underline focus-visible:ring-4 focus-visible:ring-amber-400" x-text="p.name"></a>
                                        </h3>
                                        <p class="mt-0.5 text-xs text-slate-500" x-text="p.address"></p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 rounded-lg px-2 py-0.5 text-xs font-black text-white"
                                          :style="`background-color: ${p.status_hex}`">
                                        <span x-text="p.status_color === 'green' ? '[🟢 ✔]' : (p.status_color === 'yellow' ? '[🟡 ⚠]' : '[🔴 ✖]')"></span>
                                        <span x-text="p.status_label"></span>
                                    </span>
                                </header>

                                {{-- 6 Fasilitas Utama --}}
                                <div class="mt-2.5 border-t border-dashed border-slate-200 pt-2.5">
                                    <p class="text-xs font-extrabold text-slate-700">Daftar Kesiapan 6 Fasilitas Aksesibilitas:</p>
                                    <ul class="mt-1.5 grid grid-cols-2 gap-1.5 text-xs">
                                        <li class="flex items-center gap-1.5">
                                            <span x-text="p.features && p.features.has_ramp ? '✅' : '❌'"></span>
                                            <span :class="p.features && p.features.has_ramp ? 'font-bold text-slate-900' : 'text-slate-400'">Ramp / Jalur Landai</span>
                                        </li>
                                        <li class="flex items-center gap-1.5">
                                            <span x-text="p.features && p.features.has_lift ? '✅' : '❌'"></span>
                                            <span :class="p.features && p.features.has_lift ? 'font-bold text-slate-900' : 'text-slate-400'">Lift Aksesibel</span>
                                        </li>
                                        <li class="flex items-center gap-1.5">
                                            <span x-text="p.features && p.features.has_accessible_toilet ? '✅' : '❌'"></span>
                                            <span :class="p.features && p.features.has_accessible_toilet ? 'font-bold text-slate-900' : 'text-slate-400'">Toilet Disabilitas</span>
                                        </li>
                                        <li class="flex items-center gap-1.5">
                                            <span x-text="p.features && p.features.has_guiding_block ? '✅' : '❌'"></span>
                                            <span :class="p.features && p.features.has_guiding_block ? 'font-bold text-slate-900' : 'text-slate-400'">Ubin Pemandu</span>
                                        </li>
                                        <li class="flex items-center gap-1.5">
                                            <span x-text="p.features && p.features.wide_door ? '✅' : '❌'"></span>
                                            <span :class="p.features && p.features.wide_door ? 'font-bold text-slate-900' : 'text-slate-400'">Pintu Masuk Lebar</span>
                                        </li>
                                        <li class="flex items-center gap-1.5">
                                            <span x-text="p.features && p.features.braille_lift ? '✅' : '❌'"></span>
                                            <span :class="p.features && p.features.braille_lift ? 'font-bold text-slate-900' : 'text-slate-400'">Tombol Lift Braille</span>
                                        </li>
                                    </ul>
                                </div>
                            </article>
                        </template>
                    </div>
                </section>

                {{-- Integrated Board Footer: Score readout + Legend --}}
                <div class="border-t border-slate-100 bg-slate-50 px-4 py-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Skor Rata-Rata Kota</p>
                            <div class="mt-1 flex items-center gap-1" aria-label="Skor rata-rata {{ $avgScore }} dari 100">
                                @foreach (str_split(str_pad((string) $avgScore, 2, '0', STR_PAD_LEFT)) as $digit)
                                    <span class="flap-light text-base font-extrabold">{{ $digit }}</span>
                                @endforeach
                                <span class="flap-light text-base text-slate-400">/</span>
                                <span class="flap-light text-base text-slate-400">1</span>
                                <span class="flap-light text-base text-slate-400">0</span>
                                <span class="flap-light text-base text-slate-400">0</span>
                            </div>
                        </div>

                        {{-- Legend items right inside board footer with explicit colorblind symbols --}}
                        <div class="flex items-center gap-3 text-[11px] font-bold text-slate-700">
                            <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rotate-45 bg-go-500" aria-hidden="true"></span> [🟢 ✔] {{ $g }} Ramah</span>
                            <span class="flex items-center gap-1.5"><span class="h-0 w-0 border-x-[4px] border-b-[8px] border-x-transparent border-b-warn-500" aria-hidden="true"></span> [🟡 ⚠] {{ $y }} Terbatas</span>
                            <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-stop-500" aria-hidden="true"></span> [🔴 ✖] {{ $r }} Berbahaya</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     STATUS LINES — 3 TIERS (RAMAH, TERBATAS, BERBAHAYA)
     Compact 3-column grid; rich wayfinding topology.
============================================================ --}}
<section class="border-t border-slate-100 bg-slate-50/70 py-10 lg:py-14" aria-labelledby="tier-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="tier-heading" class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                    Tiga tingkat, satu keputusan cepat
                </h2>
                <p class="mt-1 max-w-xl text-sm leading-relaxed text-slate-600">
                    Setiap tempat dinilai 0–100 dari enam fitur kunci untuk memastikan kesiapan sebelum berangkat.
                </p>
            </div>
        </div>

        @php
            $tiers = [
                [
                    'key' => 'green',
                    'name' => 'Ramah',
                    'badge' => 'Ramah',
                    'desc' => 'Ramp, lift, toilet disabilitas, ubin pemandu, dan pintu lebar tersedia. Mandiri untuk mayoritas pengguna.',
                    'line' => 'bg-go-500',
                    'tile' => 'bg-go-50 text-go-700 border border-go-200',
                    'num' => 'text-go-700',
                    'count' => $g,
                    'icon' => 'check'
                ],
                [
                    'key' => 'yellow',
                    'name' => 'Terbatas',
                    'badge' => 'Terbatas',
                    'desc' => 'Sebagian fasilitas ada, sebagian belum. Akses memerlukan pendampingan atau jalur memutar.',
                    'line' => 'bg-warn-500',
                    'tile' => 'bg-warn-50 text-warn-700 border border-warn-200',
                    'num' => 'text-warn-700',
                    'count' => $y,
                    'icon' => 'warning'
                ],
                [
                    'key' => 'red',
                    'name' => 'Berbahaya',
                    'badge' => 'Berbahaya',
                    'desc' => 'Fasilitas aksesibilitas minim atau rusak. Disarankan mencari alternatif lokasi lain.',
                    'line' => 'bg-stop-500',
                    'tile' => 'bg-stop-50 text-stop-700 border border-stop-200',
                    'num' => 'text-stop-700',
                    'count' => $r,
                    'icon' => 'blocked'
                ],
            ];
        @endphp

        <div class="mt-6 grid gap-5 md:grid-cols-3">
            @foreach ($tiers as $tier)
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-slate-300 hover:shadow-md">
                    {{-- Route rail top indicator --}}
                    <div class="mb-4 flex items-center gap-2">
                        <span class="h-2 w-full rounded-full {{ $tier['line'] }}"></span>
                        <span class="h-4 w-4 shrink-0 rounded-full border-2 border-white shadow-xs {{ $tier['line'] }}"></span>
                    </div>

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl {{ $tier['tile'] }}">
                                <x-pictogram :name="$tier['icon']" class="h-6 w-6" :stroke="2.2" />
                            </span>
                            <div>
                                <h3 class="font-display text-lg font-extrabold text-slate-900">{{ $tier['name'] }}</h3>
                                <x-status-badge :color="$tier['key']" size="sm" class="mt-0.5" />
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-display text-3xl font-extrabold {{ $tier['num'] }} text-tabular">{{ $tier['count'] }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">tempat</p>
                        </div>
                    </div>

                    <p class="mt-3 text-xs leading-relaxed text-slate-600">{{ $tier['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     PLACE INDEX — FILTERABLE DEPARTURE GRID
============================================================ --}}
<section id="tempat" class="border-t border-slate-100 bg-white py-10 lg:py-14" aria-labelledby="index-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="index-heading" class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                    Indeks tempat, siap dibaca
                </h2>
                <p class="mt-1 max-w-xl text-sm leading-relaxed text-slate-600">
                    Fasilitas publik dengan penilaian tertinggi saat ini. Saring sesuai kebutuhan mobilitasmu.
                </p>
            </div>

            <div x-data="{ filter: 'all' }" class="flex flex-wrap gap-1.5" role="group" aria-label="Saring tempat berdasarkan tingkat aksesibilitas">
                <button @click="filter = 'all'" :aria-pressed="filter === 'all'"
                        :class="filter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100'"
                        class="flex h-9 items-center rounded-lg px-3.5 text-xs font-extrabold transition">Semua</button>
                <button @click="filter = 'green'" :aria-pressed="filter === 'green'"
                        :class="filter === 'green' ? 'bg-go-600 text-white shadow-xs' : 'bg-slate-50 text-go-700 border border-slate-200 hover:bg-go-50'"
                        class="flex h-9 items-center gap-1.5 rounded-lg px-3.5 text-xs font-extrabold transition"><span class="h-2 w-2 rotate-45 bg-current opacity-80" aria-hidden="true"></span> Ramah</button>
                <button @click="filter = 'yellow'" :aria-pressed="filter === 'yellow'"
                        :class="filter === 'yellow' ? 'bg-warn-500 text-slate-950 shadow-xs' : 'bg-slate-50 text-warn-700 border border-slate-200 hover:bg-warn-50'"
                        class="flex h-9 items-center gap-1.5 rounded-lg px-3.5 text-xs font-extrabold transition"><span class="h-0 w-0 border-x-[4px] border-b-[7px] border-x-transparent border-b-current opacity-80" aria-hidden="true"></span> Terbatas</button>
                <button @click="filter = 'red'" :aria-pressed="filter === 'red'"
                        :class="filter === 'red' ? 'bg-stop-600 text-white shadow-xs' : 'bg-slate-50 text-stop-700 border border-slate-200 hover:bg-stop-50'"
                        class="flex h-9 items-center gap-1.5 rounded-lg px-3.5 text-xs font-extrabold transition"><span class="h-2 w-2 rounded-full bg-current opacity-80" aria-hidden="true"></span> Berbahaya</button>
            </div>
        </div>

        <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" role="list">
            @forelse ($featured as $place)
                <div role="listitem"
                     x-show="filter === 'all' || filter === '{{ $place->status_color }}'"
                     x-transition:enter="transition duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2">
                    <x-place-card :place="$place" />
                </div>
            @empty
                <p class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
                    Belum ada data tempat. Jadilah kontributor pertama.
                </p>
            @endforelse
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('map.index') }}" class="inline-flex h-12 items-center gap-2 rounded-xl bg-slate-900 px-6 text-sm font-extrabold text-white transition hover:bg-slate-800">
                <x-pictogram name="locate" class="h-4.5 w-4.5" /> Lihat Semua {{ $totalPlaces }} Tempat di Peta
            </a>
        </div>
    </div>
</section>

{{-- ============================================================
     SIX FEATURES — ACCESSIBILITY CHECKLIST
     Compact 3x2 grid.
============================================================ --}}
<section class="border-t border-slate-100 bg-slate-50/70 py-10 lg:py-14" aria-labelledby="features-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 id="features-heading" class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                Enam fasilitas yang kami periksa
            </h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-600">
                Skor kelayakan dihitung dari enam fitur fisik yang paling menentukan pengalaman pengguna di lokasi.
            </p>
        </div>

        @php
            $features = [
                ['icon' => 'ramp', 'title' => 'Ramp / Jalur Landai', 'desc' => 'Bidang miring yang aman sebagai pengganti atau pendamping tangga.'],
                ['icon' => 'lift', 'title' => 'Lift Ramah', 'desc' => 'Lift dengan pintu lega dan tombol kontrol terjangkau posisi duduk.'],
                ['icon' => 'toilet', 'title' => 'Toilet Disabilitas', 'desc' => 'Toilet dengan ruang putar kursi roda dan pegangan tangan kokoh.'],
                ['icon' => 'guiding-block', 'title' => 'Ubin Pemandu', 'desc' => 'Jalur ubin taktil bertekstur yang memandu langkah tunanetra.'],
                ['icon' => 'door', 'title' => 'Pintu Lebar', 'desc' => 'Bukaan pintu lebih dari 90 cm untuk leluasa dilalui kursi roda.'],
                ['icon' => 'braille', 'title' => 'Tombol Braille', 'desc' => 'Penanda huruf timbul sensorik pada tombol lift dan signage.'],
            ];
        @endphp

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" role="list">
            @foreach ($features as $i => $feature)
                <div role="listitem"
                     class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-route-cyan/40 hover:shadow-md">
                    <div class="flex items-start justify-between">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-route-cyan/10 text-route-cyan-ink transition group-hover:bg-route-cyan group-hover:text-white">
                            <x-pictogram :name="$feature['icon']" class="h-6 w-6" :stroke="2.2" />
                        </span>
                        <span class="font-display text-3xl font-extrabold text-slate-200">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="mt-3 font-display text-base font-extrabold text-slate-900">{{ $feature['title'] }}</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     BEFORE / AFTER — INTERACTIVE JOURNEY COMPARISON
============================================================ --}}
<section class="border-t border-slate-100 bg-white py-10 lg:py-14" aria-labelledby="journey-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid items-center gap-8 lg:grid-cols-2">
            <div>
                <h2 id="journey-heading" class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                    Tanpa informasi, perjalanan jadi penuh tebak-tebakan
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Pengguna kursi roda terjebak tangga tanpa ramp. Tunanetra kehilangan arah tanpa ubin pemandu.
                    AksesKita mengubah ketidakpastian itu menjadi data terbuka yang bisa diandalkan.
                </p>

                {{-- Two routes comparison list --}}
                <div class="mt-6 space-y-3">
                    <div class="flex gap-3.5 rounded-xl border border-stop-200 bg-stop-50/50 p-3.5">
                        <div class="flex flex-col items-center pt-1" aria-hidden="true">
                            <span class="h-3 w-3 rounded-full bg-stop-500 ring-4 ring-stop-500/20"></span>
                            <span class="my-1 w-0.5 flex-1 rounded-full bg-stop-400/40"></span>
                        </div>
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-wider text-stop-700">Rute Tanpa AksesKita</p>
                            <ul class="mt-1 space-y-1 text-xs text-slate-700">
                                <li class="flex items-start gap-1.5"><x-pictogram name="blocked" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-stop-600" /> Tidak tahu apakah stasiun atau mal punya lift berfungsi.</li>
                                <li class="flex items-start gap-1.5"><x-pictogram name="blocked" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-stop-600" /> Waktu terbuang mencari jalan memutar darurat di lokasi.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex gap-3.5 rounded-xl border border-emerald-200 bg-emerald-50/50 p-3.5">
                        <div class="flex flex-col items-center pt-1" aria-hidden="true">
                            <span class="h-3 w-3 rounded-full bg-go-500 ring-4 ring-go-500/20"></span>
                            <span class="my-1 w-0.5 flex-1 rounded-full bg-go-400/40"></span>
                        </div>
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Rute Bersama AksesKita</p>
                            <ul class="mt-1 space-y-1 text-xs text-slate-700">
                                <li class="flex items-start gap-1.5"><x-pictogram name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-emerald-700" /> Cek skor &amp; checklist fasilitas lengkap sebelum berangkat.</li>
                                <li class="flex items-start gap-1.5"><x-pictogram name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-emerald-700" /> Perjalanan tenang, mandiri, dan dapat direncanakan pasti.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Interactive Before/After Visual Slider --}}
            <div x-data="{ pos: 50 }"
                 x-init="$nextTick(() => { if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) pos = 100; })">
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 shadow-md">
                    {{-- Before view: Problem state with mock barrier card --}}
                    <div class="flex min-h-[28rem] sm:min-h-[30rem] flex-col justify-between bg-slate-100 p-5 sm:p-6 text-slate-900 gap-4">
                        {{-- Top status badge --}}
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-stop-200 bg-stop-50 px-2.5 py-1 text-xs font-extrabold uppercase tracking-wide text-stop-700">
                                <x-pictogram name="blocked" class="h-3.5 w-3.5 text-stop-600" /> Kondisi Awal: Tanpa AksesKita
                            </span>
                            <span class="rounded-md bg-stop-100 px-2.5 py-0.5 text-xs font-extrabold text-stop-800 shrink-0">Status: Tidak Pasti</span>
                        </div>

                        {{-- Mock barrier notification card --}}
                        <div class="rounded-xl border-2 border-slate-200 bg-white p-3.5 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <div>
                                    <p class="font-display text-sm font-black text-slate-900">Stasiun Tujuan</p>
                                    <p class="text-xs font-semibold text-slate-600">Pintu Masuk Barat</p>
                                </div>
                                <span class="rounded border border-stop-300 bg-stop-100 px-2 py-0.5 text-xs font-extrabold text-stop-700">Hambatan Fisik</span>
                            </div>
                            <div class="mt-2.5 space-y-1.5 text-xs text-slate-700">
                                <div class="flex items-center gap-2 text-stop-700 font-bold">
                                    <x-pictogram name="blocked" class="h-3.5 w-3.5 shrink-0 text-stop-600" />
                                    <span class="break-words">Tangga 28 trap tanpa ramp alternatif</span>
                                </div>
                                <div class="flex items-center gap-2 text-stop-700 font-bold">
                                    <x-pictogram name="blocked" class="h-3.5 w-3.5 shrink-0 text-stop-600" />
                                    <span class="break-words">Lift peron dalam perbaikan teknis</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-800 font-medium">
                                    <x-pictogram name="info" class="h-3.5 w-3.5 shrink-0 text-slate-600" />
                                    <span class="break-words">Tidak ada informasi sebelum berangkat</span>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom quote --}}
                        <div class="rounded-xl border border-slate-200/90 bg-white/95 p-3.5 shadow-xs">
                            <p class="font-display text-base font-black leading-snug text-slate-950 break-words">&ldquo;Sampai di lokasi baru tahu tidak bisa naik peron.&rdquo;</p>
                            <p class="mt-1 text-xs font-bold text-stop-700 leading-relaxed break-words">Waktu terbuang 45+ menit, rasa cemas &amp; harus mencari bantuan.</p>
                        </div>
                    </div>

                    {{-- After view: Solution state with verified accessibility checklist --}}
                    <div class="absolute inset-0 flex flex-col justify-between bg-go-700 p-5 sm:p-6 text-white gap-4"
                         :style="`clip-path: inset(0 ${100 - pos}% 0 0)`">
                        {{-- Top status badge --}}
                        <div class="flex items-center justify-between gap-2">
                            {{-- Badge kiri: solid putih + teks go-800 → kontras ~8.5:1 ✅ --}}
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-white px-2.5 py-1 text-xs font-extrabold uppercase tracking-wide text-go-700">
                                <x-pictogram name="check" class="h-3.5 w-3.5" /> Rute Terencana
                            </span>
                            {{-- Badge kanan: solid putih + teks go-800 → kontras ~8.5:1 ✅, naik dari 10px ke text-xs --}}
                            <span class="rounded-md bg-white px-2 py-0.5 text-xs font-bold text-go-700 shrink-0">Skor: 92/100 · Ramah</span>
                        </div>

                        {{-- Mock verified access card — border & bg diperkuat --}}
                        <div class="rounded-xl border-2 border-white/70 bg-white/25 p-3.5 shadow-lg">
                            <div class="flex items-center justify-between border-b border-white/40 pb-2">
                                <div>
                                    <p class="font-display text-sm font-extrabold text-white">Stasiun Tujuan</p>
                                    {{-- Naik dari text-white/80 (80% opacity) → text-white (100%) --}}
                                    <p class="text-xs text-white">Jalur Ramah Kursi Roda (Pintu Timur)</p>
                                </div>
                                {{-- Badge "Terverifikasi": solid putih + teks gelap, naik dari 10px → text-xs --}}
                                <span class="rounded bg-white px-2 py-0.5 text-xs font-extrabold text-go-700">Terverifikasi</span>
                            </div>
                            {{-- Item list: dari text-white/95 → text-white penuh (100%) --}}
                            <div class="mt-2.5 space-y-1.5 text-xs text-white">
                                <div class="flex items-center gap-2">
                                    <x-pictogram name="check" class="h-3.5 w-3.5 shrink-0 text-white" />
                                    <span class="break-words">Ramp landai kemiringan 1:12 siap dilalui</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-pictogram name="check" class="h-3.5 w-3.5 shrink-0 text-white" />
                                    <span class="break-words">Lift ramah dengan tombol Braille aktif</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-pictogram name="check" class="h-3.5 w-3.5 shrink-0 text-white" />
                                    <span class="break-words">Ubin pemandu menyambung hingga peron</span>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom quote — border lebih jelas --}}
                        <div class="border-t border-white/50 pt-2">
                            <p class="font-display text-base font-extrabold leading-normal text-white break-words">&ldquo;Saya cek aplikasinya dulu—langsung lewat jalur ramah.&rdquo;</p>
                            {{-- Dari text-emerald-100 → text-white agar kontras penuh --}}
                            <p class="mt-1 text-xs text-white leading-relaxed break-words">Perjalanan mandiri, rute terencana, ramah untuk semua.</p>
                        </div>
                    </div>

                    {{-- Divider handle line --}}
                    <div class="pointer-events-none absolute inset-y-0 w-0.5 bg-white shadow-lg" :style="`left: ${pos}%`">
                        <div class="absolute left-1/2 top-1/2 grid h-8 w-8 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white text-slate-900 shadow-md border border-slate-200">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7 15 4 12l3-3M13 5l3 3-3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>

                    <input type="range" min="0" max="100" x-model.number="pos"
                           class="absolute inset-0 h-full w-full cursor-ew-resize opacity-0"
                           aria-label="Geser untuk membandingkan perjalanan sebelum dan sesudah pakai AksesKita">
                </div>
                <p class="mt-2.5 text-center text-xs font-bold text-slate-700">Geser untuk membandingkan &middot; atau gunakan tombol panah</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     HOW IT WORKS — 4 STEPS (CARA KERJA)
============================================================ --}}
<section id="cara-kerja" class="border-t border-slate-100 bg-slate-50/70 py-10 lg:py-14" aria-labelledby="steps-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 id="steps-heading" class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                Empat langkah, satu kota yang lebih ramah
            </h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-600">
                Siapa pun bisa menjadi kontributor. Cukup amati, catat, dan kirim &mdash; admin memverifikasi
                sebelum data tayang ke publik.
            </p>
        </div>

        @php
            $steps = [
                ['icon' => 'vision', 'title' => 'Amati', 'desc' => 'Kunjungi fasilitas umum dan perhatikan fitur aksesibilitas yang ada.', 'tile' => 'bg-route-cyan/10 text-route-cyan-ink group-hover:bg-route-cyan group-hover:text-white', 'chip' => 'bg-route-cyan-ink'],
                ['icon' => 'report', 'title' => 'Foto & Catat', 'desc' => 'Ambil foto bukti kondisi ramp, lift, toilet, atau ubin pemandu.', 'tile' => 'bg-route-violet/10 text-route-violet group-hover:bg-route-violet group-hover:text-white', 'chip' => 'bg-route-violet'],
                ['icon' => 'arrow', 'title' => 'Kirim Laporan', 'desc' => 'Unggah laporan lewat dashboard pengguna dalam beberapa menit saja.', 'tile' => 'bg-route-magenta/10 text-route-magenta group-hover:bg-route-magenta group-hover:text-white', 'chip' => 'bg-route-magenta'],
                ['icon' => 'check', 'title' => 'Terverifikasi', 'desc' => 'Admin meninjau laporan, dan data langsung terbit di peta publik.', 'tile' => 'bg-go-50 text-go-700 group-hover:bg-go-600 group-hover:text-white', 'chip' => 'bg-go-700'],
            ];
        @endphp

        <div class="relative mt-8">
            <div class="pointer-events-none absolute left-0 right-0 top-[2.75rem] hidden h-1 rounded-full bg-slate-200 lg:block" aria-hidden="true"></div>

            <ol class="relative grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $i => $step)
                    <li class="group relative">
                        <div class="relative h-full rounded-2xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-slate-300 hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <span class="relative grid h-12 w-12 shrink-0 place-items-center rounded-xl transition {{ $step['tile'] }}">
                                    <x-pictogram :name="$step['icon']" class="h-6 w-6" :stroke="2.2" />
                                    <span class="absolute -right-1.5 -top-1.5 grid h-5 w-5 place-items-center rounded-full {{ $step['chip'] }} font-display text-[10px] font-extrabold text-white">{{ $i + 1 }}</span>
                                </span>
                                <h3 class="font-display text-base font-extrabold text-slate-900">{{ $step['title'] }}</h3>
                            </div>
                            <p class="mt-3 text-xs leading-relaxed text-slate-600">{{ $step['desc'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('register') }}" class="inline-flex h-12 items-center gap-2 rounded-xl bg-go-600 px-6 text-sm font-extrabold text-white shadow-md shadow-go-600/20 transition hover:bg-go-700">
                <x-pictogram name="community" class="h-4.5 w-4.5" /> Mulai Jadi Kontributor
            </a>
        </div>
    </div>
</section>

{{-- ============================================================
     STATISTICS — COMPACT ELEVATED SHOWCASE
============================================================ --}}
<section id="statistik" class="border-t border-slate-100 bg-white py-10 lg:py-14" aria-labelledby="stats-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50/80 p-6 shadow-sm sm:p-8">
            <div class="grid gap-8 lg:grid-cols-[1fr_1.2fr] lg:items-center">
                <div>
                    <h2 id="stats-heading" class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                        Angka yang tumbuh dari kepedulian warga
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        Setiap laporan yang disetujui membuat peta ini makin akurat untuk pejalan kaki, pengguna kursi roda, dan lansia.
                    </p>

                    {{-- Stacked status progress bar --}}
                    <div class="mt-5">
                        <div class="flex h-3.5 w-full overflow-hidden rounded-full bg-slate-200" role="img" aria-label="Sebaran status: {{ $g }} ramah, {{ $y }} terbatas, {{ $r }} berbahaya">
                            <span style="width: {{ $g / $sum * 100 }}%; background: var(--color-go-500)"></span>
                            <span style="width: {{ $y / $sum * 100 }}%; background: var(--color-warn-500)"></span>
                            <span style="width: {{ $r / $sum * 100 }}%; background: var(--color-stop-500)"></span>
                        </div>
                        <div class="mt-2.5 flex flex-wrap gap-x-5 gap-y-1 text-xs font-bold text-slate-600">
                            <span class="flex items-center gap-1.5"><span class="h-2 w-2 rotate-45 bg-go-500" aria-hidden="true"></span> {{ $g }} Ramah</span>
                            <span class="flex items-center gap-1.5"><span class="h-0 w-0 border-x-[4px] border-b-[7px] border-x-transparent border-b-warn-500" aria-hidden="true"></span> {{ $y }} Terbatas</span>
                            <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-stop-500" aria-hidden="true"></span> {{ $r }} Berbahaya</span>
                        </div>
                    </div>

                    <a href="{{ route('map.index') }}" class="mt-6 inline-flex h-11 items-center gap-2 rounded-xl bg-slate-900 px-5 text-xs font-extrabold text-white transition hover:bg-slate-800">
                        Jelajahi Semua Tempat
                        <x-pictogram name="arrow" class="h-3.5 w-3.5" />
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @php
                        $cards = [
                            ['label' => 'Tempat Terdata', 'value' => $stats['places'] ?? 0, 'tone' => 'text-slate-900'],
                            ['label' => 'Terverifikasi', 'value' => $stats['verified'] ?? 0, 'tone' => 'text-route-cyan-ink'],
                            ['label' => 'Laporan Warga', 'value' => $stats['reports'] ?? 0, 'tone' => 'text-slate-900'],
                            ['label' => 'Ramah', 'value' => $g, 'tone' => 'text-go-700'],
                            ['label' => 'Terbatas', 'value' => $y, 'tone' => 'text-warn-700'],
                            ['label' => 'Berbahaya', 'value' => $r, 'tone' => 'text-stop-700'],
                        ];
                    @endphp
                    @foreach ($cards as $card)
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
                            <p class="font-display text-2xl font-extrabold text-tabular {{ $card['tone'] }}">{{ $card['value'] }}</p>
                            <p class="mt-0.5 text-xs font-bold uppercase tracking-wider text-slate-500">{{ $card['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     CALL TO ACTION — COMMUNITY BANNER
============================================================ --}}
<section class="pb-16 pt-2">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 px-6 py-12 text-center text-slate-900 shadow-sm sm:px-12 sm:py-14">
            <div class="relative">
                <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-route-cyan/10 text-route-cyan-ink">
                    <x-pictogram name="community" class="h-6 w-6" :stroke="2.2" />
                </span>
                <h2 class="mt-4 font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl">
                    Jadi mata &amp; telinga kota yang inklusif
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Berapa banyak fasilitas umum yang kamu lewati setiap hari? Mulai laporkan hari ini dan bantu
                    ribuan penyandang disabilitas bergerak lebih leluasa.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('register') }}" class="flex h-12 items-center gap-2 rounded-xl bg-route-cyan px-6 text-sm font-extrabold text-white shadow-md shadow-route-cyan/20 transition hover:bg-route-cyan-ink">
                        <x-pictogram name="community" class="h-4.5 w-4.5" />
                        Gabung Kontributor Sekarang
                    </a>
                    <a href="{{ route('map.index') }}" class="flex h-12 items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-sm font-extrabold text-slate-800 transition hover:bg-slate-100">
                        <x-pictogram name="locate" class="h-4.5 w-4.5" />
                        Lihat Peta Publik
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    /*
     * Alpine component for hero board:
     * - Immediate display of all content (no blank waiting periods)
     * - Typing effect on dynamic headline
     * - Interactive Leaflet map
     */
    document.addEventListener('alpine:init', () => {
        Alpine.data('heroBoard', (places = [], center = { lat: -6.21, lng: 106.78 }) => ({
            places,
            typed: 'perjalananmu lancar.',
            typingTimer: null,
            viewMode: 'map', // 'map' | 'list'
            map: null,

            init() {
                const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (!reduce) {
                    this.runTyping();
                }
                this.$nextTick(() => this.initMap());
            },

            setViewMode(mode) {
                this.viewMode = mode;
                if (mode === 'map') {
                    this.$nextTick(() => {
                        if (this.map) {
                            this.map.invalidateSize();
                        } else {
                            this.initMap();
                        }
                    });
                }
            },

            runTyping() {
                const words = ['perjalananmu lancar.', 'aksesnya jelas.', 'bebas hambatan.'];
                let wordIndex = 0, charIndex = 0, deleting = false;
                const tick = () => {
                    const word = words[wordIndex];
                    this.typed = word.substring(0, charIndex);
                    if (!deleting && charIndex < word.length) {
                        charIndex++;
                        this.typingTimer = setTimeout(tick, 75);
                    } else if (!deleting && charIndex === word.length) {
                        deleting = true;
                        this.typingTimer = setTimeout(tick, 1800);
                    } else if (deleting && charIndex > 0) {
                        charIndex--;
                        this.typingTimer = setTimeout(tick, 35);
                    } else {
                        deleting = false;
                        wordIndex = (wordIndex + 1) % words.length;
                        this.typingTimer = setTimeout(tick, 350);
                    }
                };
                tick();
            },

            initMap() {
                const el = document.getElementById('hero-map');
                if (!el || typeof window.L === 'undefined') return;
                const map = L.map(el, { center: [center.lat, center.lng], zoom: 11, zoomControl: true, scrollWheelZoom: false });
                window.AksesKitaMap.baseTiles().addTo(map);
                window.AksesKitaMap.addPlaces(map, this.places);
                if (this.places.length) {
                    const bounds = L.latLngBounds(this.places.map(p => [p.latitude, p.longitude]));
                    map.fitBounds(bounds, { padding: [30, 30], maxZoom: 13 });
                }
                map.on('focus click', () => map.scrollWheelZoom.enable());
                map.on('mouseout blur', () => map.scrollWheelZoom.disable());
                this.map = map;
            },
        }));
    });
</script>
@endpush
