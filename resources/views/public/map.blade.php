@extends('layouts.app')

@section('title', 'Peta Aksesibilitas — AksesKita')

@section('content')
<div x-data="mapExplorer(@js($mapCenter), @js($categories))" class="flex h-[100svh] flex-col bg-slate-50 pt-[68px]">
    {{-- Toolbar: Accessible Control Board --}}
    <div class="z-[1050] border-b-2 border-slate-200 bg-white" role="region" aria-label="Alat pencarian dan penyaring peta aksesibilitas">
        <div class="mx-auto flex max-w-[1600px] flex-wrap items-center justify-between gap-3 px-4 py-3 lg:px-6">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-slate-900 text-route-cyan" aria-hidden="true">
                    <x-pictogram name="locate" class="h-6 w-6" :stroke="2.2" />
                </span>
                <div>
                    <h1 class="font-display text-lg font-extrabold tracking-tight text-slate-900">Peta Aksesibilitas</h1>
                    <p class="text-xs font-semibold text-slate-500" aria-live="polite">
                        Jakarta &amp; Tangerang &middot; <span x-text="total"></span> tempat ditemukan
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Toggle View: Map Canvas vs Semantic Text List --}}
                <div class="flex items-center rounded-xl border border-slate-200 bg-slate-100 p-1" role="group" aria-label="Mode tampilan peta atau daftar teks">
                    <button type="button" @click="setViewMode('map')"
                            :aria-pressed="viewMode === 'map'"
                            :class="viewMode === 'map' ? 'bg-white text-slate-950 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                            class="flex h-9 items-center gap-1.5 rounded-lg px-3 text-xs transition focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                        <span aria-hidden="true">🗺️</span>
                        <span>Tampilan Peta</span>
                    </button>
                    <button type="button" @click="setViewMode('list')"
                            :aria-pressed="viewMode === 'list'"
                            :class="viewMode === 'list' ? 'bg-white text-slate-950 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                            class="flex h-9 items-center gap-1.5 rounded-lg px-3 text-xs transition focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                        <span aria-hidden="true">📋</span>
                        <span>Tampilan Daftar Teks</span>
                    </button>
                </div>

                {{-- Search Input --}}
                <div class="relative">
                    <label for="map-search" class="sr-only">Cari nama atau alamat tempat</label>
                    <x-pictogram name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input id="map-search" type="search" x-model.debounce.400ms="search" @input="load()"
                           placeholder="Cari nama tempat&hellip;"
                           aria-label="Cari nama tempat atau alamat fasilitas"
                           class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-xs font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-2 focus:ring-route-cyan focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none sm:w-52">
                </div>

                {{-- Custom Category Filter Dropdown --}}
                <div class="relative" @click.outside="openCategory = false" @keydown.escape.window="openCategory = false">
                    <label id="category-label" class="sr-only">Saring berdasarkan kategori fasilitas</label>
                    <button type="button"
                            @click="openCategory = !openCategory; if (openCategory) openStatus = false"
                            :aria-expanded="openCategory"
                            aria-haspopup="listbox"
                            aria-labelledby="category-label"
                            class="flex h-10 items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs font-bold text-slate-900 shadow-2xs transition hover:border-slate-300 hover:bg-slate-100 focus:border-route-cyan focus:bg-white focus:ring-2 focus:ring-route-cyan focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                        <span class="flex items-center gap-1.5 truncate max-w-[130px] sm:max-w-none">
                            <span class="text-slate-400" aria-hidden="true">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </span>
                            <span x-text="categoryLabels[category] || 'Semua kategori'" class="truncate"></span>
                        </span>
                        <svg class="h-3.5 w-3.5 text-slate-400 transition-transform duration-200 shrink-0"
                             :class="{ 'rotate-180 text-slate-900': openCategory }"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Panel --}}
                    <div x-show="openCategory"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         x-cloak
                         role="listbox"
                         aria-labelledby="category-label"
                         class="absolute left-0 top-full z-[1200] mt-1.5 w-60 max-h-80 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl ring-1 ring-black/5 focus:outline-none">
                        <button type="button"
                                @click="selectCategory('all')"
                                role="option"
                                :aria-selected="category === 'all'"
                                :class="category === 'all' ? 'bg-slate-900 font-extrabold text-white' : 'text-slate-700 hover:bg-slate-100 font-bold'"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-xs transition text-left">
                            <span class="flex items-center gap-2">
                                <span aria-hidden="true">🌐</span>
                                <span>Semua kategori</span>
                            </span>
                            <span x-show="category === 'all'" aria-hidden="true" class="font-black text-white">✔</span>
                        </button>
                        <div class="my-1 border-t border-slate-100"></div>
                        @foreach ($categories as $key => $label)
                            <button type="button"
                                    @click="selectCategory('{{ $key }}')"
                                    role="option"
                                    :aria-selected="category === '{{ $key }}'"
                                    :class="category === '{{ $key }}' ? 'bg-slate-900 font-extrabold text-white' : 'text-slate-700 hover:bg-slate-100 font-bold'"
                                    class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-xs transition text-left">
                                <span class="flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 rounded-full" :class="category === '{{ $key }}' ? 'bg-white' : 'bg-slate-400'"></span>
                                    <span>{{ $label }}</span>
                                </span>
                                <span x-show="category === '{{ $key }}'" aria-hidden="true" class="font-black text-white">✔</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Custom Status Filter Dropdown --}}
                <div class="relative" @click.outside="openStatus = false" @keydown.escape.window="openStatus = false">
                    <label id="status-label" class="sr-only">Saring berdasarkan tingkat kelayakan aksesibilitas</label>
                    <button type="button"
                            @click="openStatus = !openStatus; if (openStatus) openCategory = false"
                            :aria-expanded="openStatus"
                            aria-haspopup="listbox"
                            aria-labelledby="status-label"
                            class="flex h-10 items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs font-bold text-slate-900 shadow-2xs transition hover:border-slate-300 hover:bg-slate-100 focus:border-route-cyan focus:bg-white focus:ring-2 focus:ring-route-cyan focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                        <span class="flex items-center gap-2 truncate max-w-[140px] sm:max-w-none">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full shrink-0"
                                  :class="{
                                      'bg-slate-400': status === 'all',
                                      'bg-emerald-500 ring-2 ring-emerald-200': status === 'green',
                                      'bg-amber-500 ring-2 ring-amber-200': status === 'yellow',
                                      'bg-rose-500 ring-2 ring-rose-200': status === 'red'
                                  }"></span>
                            <span x-text="statusLabels[status]?.label || 'Semua status'" class="truncate"></span>
                        </span>
                        <svg class="h-3.5 w-3.5 text-slate-400 transition-transform duration-200 shrink-0"
                             :class="{ 'rotate-180 text-slate-900': openStatus }"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Panel --}}
                    <div x-show="openStatus"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         x-cloak
                         role="listbox"
                         aria-labelledby="status-label"
                         class="absolute left-0 sm:right-0 sm:left-auto top-full z-[1200] mt-1.5 w-72 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xl ring-1 ring-black/5 focus:outline-none">
                        
                        <button type="button"
                                @click="selectStatus('all')"
                                role="option"
                                :aria-selected="status === 'all'"
                                :class="status === 'all' ? 'bg-slate-900 text-white font-extrabold' : 'text-slate-700 hover:bg-slate-100 font-bold'"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-xs transition text-left">
                            <div class="flex items-center gap-2.5">
                                <span class="grid h-6 w-6 place-items-center rounded-lg bg-slate-100 text-slate-700 text-xs" :class="status === 'all' ? 'bg-white/20 text-white' : ''">📊</span>
                                <div>
                                    <div class="font-bold">Semua status</div>
                                    <div class="text-[11px] opacity-75">Tampilkan semua kondisi akses</div>
                                </div>
                            </div>
                            <span x-show="status === 'all'" aria-hidden="true" class="font-black text-white">✔</span>
                        </button>

                        <div class="my-1 border-t border-slate-100"></div>

                        <button type="button"
                                @click="selectStatus('green')"
                                role="option"
                                :aria-selected="status === 'green'"
                                :class="status === 'green' ? 'bg-emerald-50 border border-emerald-200 text-emerald-950 font-extrabold' : 'text-slate-700 hover:bg-emerald-50/60 font-bold'"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-xs transition text-left mt-0.5">
                            <div class="flex items-start gap-2.5">
                                <span class="mt-0.5 inline-flex h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                                <div>
                                    <div class="flex items-center gap-1.5 font-black text-emerald-900">
                                        <span>Ramah Mandiri</span>
                                        <span class="rounded bg-emerald-100 px-1.5 py-0.2 text-[10px] text-emerald-800 font-extrabold">Skor 75-100</span>
                                    </div>
                                    <div class="text-[11px] font-semibold text-emerald-800/80">Ramah kursi roda & mandiri</div>
                                </div>
                            </div>
                            <span x-show="status === 'green'" aria-hidden="true" class="font-black text-emerald-700">✔</span>
                        </button>

                        <button type="button"
                                @click="selectStatus('yellow')"
                                role="option"
                                :aria-selected="status === 'yellow'"
                                :class="status === 'yellow' ? 'bg-amber-50 border border-amber-200 text-amber-950 font-extrabold' : 'text-slate-700 hover:bg-amber-50/60 font-bold'"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-xs transition text-left mt-0.5">
                            <div class="flex items-start gap-2.5">
                                <span class="mt-0.5 inline-flex h-2.5 w-2.5 shrink-0 rounded-full bg-amber-500 ring-4 ring-amber-100"></span>
                                <div>
                                    <div class="flex items-center gap-1.5 font-black text-amber-900">
                                        <span>Akses Terbatas</span>
                                        <span class="rounded bg-amber-100 px-1.5 py-0.2 text-[10px] text-amber-800 font-extrabold">Skor 50-74</span>
                                    </div>
                                    <div class="text-[11px] font-semibold text-amber-800/80">Perlu pendampingan</div>
                                </div>
                            </div>
                            <span x-show="status === 'yellow'" aria-hidden="true" class="font-black text-amber-700">✔</span>
                        </button>

                        <button type="button"
                                @click="selectStatus('red')"
                                role="option"
                                :aria-selected="status === 'red'"
                                :class="status === 'red' ? 'bg-rose-50 border border-rose-200 text-rose-950 font-extrabold' : 'text-slate-700 hover:bg-rose-50/60 font-bold'"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-xs transition text-left mt-0.5">
                            <div class="flex items-start gap-2.5">
                                <span class="mt-0.5 inline-flex h-2.5 w-2.5 shrink-0 rounded-full bg-rose-500 ring-4 ring-rose-100"></span>
                                <div>
                                    <div class="flex items-center gap-1.5 font-black text-rose-900">
                                        <span>Belum Memadai</span>
                                        <span class="rounded bg-rose-100 px-1.5 py-0.2 text-[10px] text-rose-800 font-extrabold">Skor &lt;50</span>
                                    </div>
                                    <div class="text-[11px] font-semibold text-rose-800/80">Curam / berisiko</div>
                                </div>
                            </div>
                            <span x-show="status === 'red'" aria-hidden="true" class="font-black text-rose-700">✔</span>
                        </button>
                    </div>
                </div>

                {{-- Reset Filters Button (appears when filters are active) --}}
                <button x-show="category !== 'all' || status !== 'all' || search !== ''"
                        x-cloak
                        @click="resetFilters()"
                        type="button"
                        aria-label="Reset semua filter pencarian"
                        class="flex h-10 items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-2.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100 focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="hidden sm:inline">Reset</span>
                </button>

                {{-- Geolocation Button --}}
                <button @click="locateMe()" type="button"
                        aria-label="Pusatkan peta ke koordinat lokasi GPS saya saat ini"
                        class="flex h-10 items-center gap-1.5 rounded-xl bg-route-cyan-ink px-3.5 text-xs font-black text-white shadow-xs transition hover:bg-route-cyan focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                    <x-pictogram name="locate" class="h-4 w-4" />
                    <span>Lokasiku</span>
                </button>
            </div>
        </div>
    </div>

    {{-- VIEW 1: Interactive Map Canvas + Quick Sidebar --}}
    <div x-show="viewMode === 'map'" class="relative flex flex-1 overflow-hidden" role="region" aria-label="Kanvas peta interaktif">
        <div id="full-map" class="h-full flex-1" tabindex="0" role="application" aria-label="Peta interaktif Leaflet. Gunakan panah keyboard atau kontrol zoom untuk bernavigasi."></div>

        {{-- Place list sidebar on map --}}
        <aside aria-label="Daftar tempat pada peta" class="absolute inset-y-0 right-0 z-[1040] w-full max-w-sm overflow-y-auto border-l-2 border-slate-200 bg-white lg:static">
            <div class="sticky top-0 z-10 border-b border-slate-200 bg-white px-4 py-3">
                <p class="font-display text-sm font-extrabold text-slate-900">Hasil Tempat (<span x-text="total"></span>)</p>
                <p class="text-xs font-semibold text-slate-500">Pilih kartu untuk menyorot pin di peta</p>
            </div>

            <div class="space-y-3 p-4">
                <template x-for="place in places" :key="place.id">
                    <button type="button" @click="focusPlace(place)"
                            :aria-label="'Pusatkan peta ke ' + place.name + ', Skor ' + place.overall_score + ' dari 100, Status ' + place.status_label"
                            class="w-full rounded-2xl border-2 border-slate-200 bg-white p-4 text-left transition hover:-translate-y-0.5 hover:border-route-cyan hover:shadow-md focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[11px] font-extrabold uppercase tracking-wide text-route-cyan-ink" x-text="place.category_label"></p>
                                <p class="mt-0.5 truncate font-display font-extrabold text-slate-900" x-text="place.name"></p>
                                <p class="mt-1 line-clamp-1 text-xs text-slate-500" x-text="place.address"></p>
                            </div>
                            <span class="flap h-12 !min-w-12 text-base font-black text-white" :style="`background:${place.status_hex}`" x-text="place.overall_score"></span>
                        </div>
                        <div class="mt-3 flex items-center justify-between border-t border-dashed border-slate-200 pt-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-extrabold" :style="`color:${place.status_hex}`">
                                <span x-text="place.status_color === 'green' ? '[🟢 ✔]' : (place.status_color === 'yellow' ? '[🟡 ⚠]' : '[🔴 ✖]')"></span>
                                <span x-text="place.status_label"></span>
                            </span>
                            <span class="flex items-center gap-1 text-xs font-black text-slate-900">Sorot Peta <x-pictogram name="arrow" class="h-3.5 w-3.5" /></span>
                        </div>
                    </button>
                </template>

                <p x-show="!loading && places.length === 0" class="py-10 text-center text-sm font-semibold text-slate-500" role="status">
                    Tidak ada tempat yang cocok dengan saringan saat ini.
                </p>
                <p x-show="loading" class="py-6 text-center text-sm font-semibold text-slate-500" role="status" aria-live="polite">Memuat data peta&hellip;</p>
            </div>
        </aside>
    </div>

    {{-- VIEW 2: Semantic HTML Text List (Khusus Screen Reader NVDA/VoiceOver & Aksesibilitas) --}}
    <section x-show="viewMode === 'list'"
             x-cloak
             role="region"
             aria-label="Daftar Aksesibilitas Fasilitas Publik (Tampilan Teks Semantik)"
             class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-4" aria-live="polite">
                <div>
                    <h2 class="text-xl font-black text-slate-950">Daftar Hasil Aksesibilitas (Tampilan Teks)</h2>
                    <p class="mt-0.5 text-xs text-slate-600">
                        Menampilkan <strong x-text="places.length"></strong> dari total <strong x-text="total"></strong> fasilitas publik.
                    </p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800">
                    Struktur semantik lengkap untuk Screen Reader (NVDA, JAWS, VoiceOver).
                </div>
            </div>

            {{-- Empty & loading states --}}
            <div x-show="loading" class="py-12 text-center" role="status" aria-live="polite">
                <p class="text-sm font-bold text-slate-600">Sedang memuat daftar tempat&hellip;</p>
            </div>

            <div x-show="!loading && places.length === 0" class="rounded-2xl border-2 border-dashed border-slate-300 bg-white p-12 text-center" role="status">
                <p class="text-base font-extrabold text-slate-800">Tidak ada fasilitas yang sesuai dengan pencarian / filter.</p>
                <p class="mt-1 text-xs text-slate-500">Coba ubah kata kunci pencarian atau setel filter ke "Semua Kategori" dan "Semua Status".</p>
                <button type="button" @click="resetFilters()" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800 focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                    <span>Reset Filter</span>
                </button>
            </div>

            {{-- Semantic Card List --}}
            <div x-show="!loading && places.length > 0" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <template x-for="place in places" :key="'list-item-' + place.id">
                    <article class="flex flex-col justify-between rounded-2xl border-2 border-slate-200 bg-white p-5 shadow-xs transition hover:border-route-cyan focus-within:ring-4 focus-within:ring-amber-400"
                             :aria-labelledby="'semantic-title-' + place.id">
                        <div>
                            <header class="border-b border-slate-100 pb-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-black uppercase tracking-wide text-slate-700" x-text="place.category_label"></span>
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-black text-white"
                                          :style="`background-color: ${place.status_hex}`"
                                          :aria-label="'Status kelayakan: ' + place.status_label">
                                        <span x-text="place.status_color === 'green' ? '✔' : (place.status_color === 'yellow' ? '⚠' : '✖')"></span>
                                        <span x-text="place.status_label"></span>
                                    </span>
                                </div>
                                <h3 :id="'semantic-title-' + place.id" class="mt-2.5 text-lg font-black text-slate-900" x-text="place.name"></h3>
                                <p class="mt-1 text-xs text-slate-600" x-text="place.address"></p>
                                <p class="mt-2 text-xs font-bold text-slate-700" :aria-label="'Skor total kelayakan: ' + place.overall_score + ' dari 100'">
                                    Skor Kelayakan: <strong class="text-sm font-black text-slate-950" x-text="place.overall_score + '/100'"></strong>
                                </p>
                            </header>

                            {{-- 6 Key Facilities Checklist (Semantik <ul> & <li>) --}}
                            <div class="mt-3.5">
                                <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-500">Checklist 6 Fasilitas Disabilitas:</h4>
                                <ul class="mt-2 space-y-1.5 text-xs" role="list" :aria-label="'Checklist fasilitas disabilitas untuk ' + place.name">
                                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-1.5">
                                        <span class="font-bold text-slate-700">Ramp / Bidang Miring</span>
                                        <span class="font-extrabold" :class="place.features?.has_ramp ? 'text-emerald-700' : 'text-rose-700'"
                                              x-text="place.features?.has_ramp ? '✔ Tersedia' : '✖ Tidak Ada'"></span>
                                    </li>
                                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-1.5">
                                        <span class="font-bold text-slate-700">Lift Ramah Disabilitas</span>
                                        <span class="font-extrabold" :class="place.features?.has_lift ? 'text-emerald-700' : 'text-rose-700'"
                                              x-text="place.features?.has_lift ? '✔ Tersedia' : '✖ Tidak Ada'"></span>
                                    </li>
                                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-1.5">
                                        <span class="font-bold text-slate-700">Toilet Khusus Disabilitas</span>
                                        <span class="font-extrabold" :class="place.features?.has_accessible_toilet ? 'text-emerald-700' : 'text-rose-700'"
                                              x-text="place.features?.has_accessible_toilet ? '✔ Tersedia' : '✖ Tidak Ada'"></span>
                                    </li>
                                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-1.5">
                                        <span class="font-bold text-slate-700">Ubin Pemandu (Guiding Block)</span>
                                        <span class="font-extrabold" :class="place.features?.has_guiding_block ? 'text-emerald-700' : 'text-rose-700'"
                                              x-text="place.features?.has_guiding_block ? '✔ Tersedia' : '✖ Tidak Ada'"></span>
                                    </li>
                                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-1.5">
                                        <span class="font-bold text-slate-700">Pintu Masuk Lebar (&gt;90cm)</span>
                                        <span class="font-extrabold" :class="place.features?.wide_door ? 'text-emerald-700' : 'text-rose-700'"
                                              x-text="place.features?.wide_door ? '✔ Tersedia' : '✖ Tidak Ada'"></span>
                                    </li>
                                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-1.5">
                                        <span class="font-bold text-slate-700">Tombol / Panel Braille</span>
                                        <span class="font-extrabold" :class="place.features?.braille_lift ? 'text-emerald-700' : 'text-rose-700'"
                                              x-text="place.features?.braille_lift ? '✔ Tersedia' : '✖ Tidak Ada'"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <footer class="mt-5 border-t border-slate-100 pt-3">
                            <a :href="place.url"
                               :aria-label="'Buka halaman detail fasilitas dan laporan warga untuk ' + place.name"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-black text-white transition hover:bg-route-cyan focus-visible:ring-4 focus-visible:ring-amber-400 focus-visible:outline-none">
                                <span>Lihat Observasi &amp; Foto Lengkap &rarr;</span>
                            </a>
                        </footer>
                    </article>
                </template>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mapExplorer', (center = { lat: -6.21, lng: 106.78 }, categories = {}) => ({
            map: null,
            markerLayer: null,
            markers: {},
            places: [],
            total: 0,
            loading: false,
            category: 'all',
            status: 'all',
            search: '',
            viewMode: 'map', // 'map' | 'list'
            center,
            openCategory: false,
            openStatus: false,
            categoryLabels: {
                all: 'Semua kategori',
                ...categories
            },
            statusLabels: {
                all: { label: 'Semua status', badge: '📊' },
                green: { label: 'Ramah (75-100)', badge: '🟢' },
                yellow: { label: 'Terbatas (50-74)', badge: '🟡' },
                red: { label: 'Berbahaya (<50)', badge: '🔴' }
            },

            selectCategory(val) {
                this.category = val;
                this.openCategory = false;
                this.load();
            },

            selectStatus(val) {
                this.status = val;
                this.openStatus = false;
                this.load();
            },

            resetFilters() {
                this.category = 'all';
                this.status = 'all';
                this.search = '';
                this.openCategory = false;
                this.openStatus = false;
                this.load();
            },

            init() {
                this.$nextTick(() => this.initMap());
            },

            setViewMode(mode) {
                this.viewMode = mode;
                if (mode === 'map') {
                    this.$nextTick(() => {
                        if (this.map) {
                            this.map.invalidateSize();
                        }
                    });
                }
            },

            initMap() {
                const el = document.getElementById('full-map');
                if (!el || typeof window.L === 'undefined') return;

                this.map = L.map(el, { center: [this.center.lat, this.center.lng], zoom: 12 });
                window.AksesKitaMap.baseTiles().addTo(this.map);
                window.AksesKitaMap.renderLegend().addTo(this.map);

                this.markerLayer = L.featureGroup().addTo(this.map);
                this.load();
            },

            async load() {
                this.loading = true;
                const params = new URLSearchParams({
                    category: this.category,
                    status: this.status,
                    search: this.search,
                });

                try {
                    const res = await fetch(`{{ route('api.places') }}?${params.toString()}`, {
                        headers: { Accept: 'application/json' },
                    });
                    const data = await res.json();
                    this.places = data.places;
                    this.total = data.count;
                    this.renderMarkers();
                } catch (e) {
                    console.error('Gagal memuat data peta', e);
                } finally {
                    this.loading = false;
                }
            },

            renderMarkers() {
                if (!this.map) return;
                this.markerLayer.clearLayers();
                this.markers = {};

                const group = window.AksesKitaMap.addPlaces(this.map, this.places);
                this.places.forEach((place, i) => {
                    const layer = group.getLayers()[i];
                    if (layer) {
                        this.markers[place.id] = layer;
                        this.markerLayer.addLayer(layer);
                    }
                });

                if (this.places.length) {
                    const bounds = L.latLngBounds(this.places.map(p => [p.latitude, p.longitude]));
                    this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
                }
            },

            focusPlace(place) {
                if (!this.map) return;
                this.map.setView([place.latitude, place.longitude], 15, { animate: true });
                const marker = this.markers[place.id];
                if (marker) marker.openPopup();
            },

            locateMe() {
                if (!navigator.geolocation || !this.map) return;
                navigator.geolocation.getCurrentPosition((pos) => {
                    const { latitude, longitude } = pos.coords;
                    this.map.setView([latitude, longitude], 15);
                    L.circleMarker([latitude, longitude], {
                        radius: 8, color: '#0891b2', fillColor: '#06b6d4', fillOpacity: 0.9,
                    }).addTo(this.map).bindPopup('Lokasi kamu saat ini').openPopup();
                });
            },
        }));
    });
</script>
@endpush
