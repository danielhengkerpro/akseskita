@extends('layouts.app')

@section('title', $place->name . ' — AksesKita')

@php
    $region = match ($place->status_color) {
        'green' => ['bar' => 'bg-go-600', 'field' => 'bg-go-700', 'soft' => 'bg-go-50', 'ink' => 'text-go-700', 'ring' => 'ring-go-600/25'],
        'red' => ['bar' => 'bg-stop-600', 'field' => 'bg-stop-700', 'soft' => 'bg-stop-50', 'ink' => 'text-stop-700', 'ring' => 'ring-stop-600/25'],
        default => ['bar' => 'bg-warn-600', 'field' => 'bg-warn-700', 'soft' => 'bg-warn-50', 'ink' => 'text-warn-700', 'ring' => 'ring-warn-600/25'],
    };
@endphp

@section('content')
<div class="board-ground pt-28 pb-20 text-white lg:pt-32">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb: a wayfinding trail --}}
        <nav aria-label="Jejak navigasi" class="flex flex-wrap items-center gap-2 text-sm text-white/60">
            <a href="{{ route('home') }}" class="font-bold transition hover:text-route-cyan-soft">Beranda</a>
            <x-pictogram name="arrow" class="h-3.5 w-3.5 text-white/60" />
            <a href="{{ route('map.index') }}" class="font-bold transition hover:text-route-cyan-soft">Peta</a>
            <x-pictogram name="arrow" class="h-3.5 w-3.5 text-white/60" />
            <span class="font-bold text-white">{{ $place->name }}</span>
        </nav>

        {{-- Signage header: place name on its status field --}}
        <div class="mt-6 overflow-hidden rounded-[2rem] border-2 border-white/10">
            <div class="relative {{ $region['field'] }} p-7 sm:p-9">
                <div class="pointer-events-none absolute inset-0 opacity-[0.1]" aria-hidden="true"
                     style="background-image: radial-gradient(#fff 2px, transparent 2.4px); background-size: 18px 18px;"></div>
                <div class="relative flex flex-wrap items-center gap-3">
                    <x-status-badge :color="$place->status_color" size="lg" class="!bg-white" />
                    <span class="rounded-lg bg-board-950/45 px-3 py-1.5 text-sm font-extrabold text-white">{{ $place->categoryLabel() }}</span>
                    @if ($place->is_verified)
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-board-950/45 px-3 py-1.5 text-sm font-extrabold text-white">
                            <x-pictogram name="check" class="h-4 w-4" /> Terverifikasi
                        </span>
                    @endif
                </div>
                <h1 class="relative mt-5 font-display text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl">{{ $place->name }}</h1>
                <p class="relative mt-4 flex items-start gap-2 text-white/90">
                    <x-pictogram name="pin" class="mt-0.5 h-5 w-5 shrink-0" />
                    {{ $place->address }}
                </p>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1.35fr_1fr]">
            {{-- Left: the feature checklist --}}
            <div>
                <section aria-labelledby="facilities-heading" class="rounded-3xl border-2 border-ink-900/5 bg-white p-6 text-ink-900 shadow-sm sm:p-7">
                    <h2 id="facilities-heading" class="font-display text-xl font-extrabold text-ink-900">Fasilitas aksesibilitas</h2>
                    <p class="mt-1.5 text-sm text-ink-500">Enam fitur yang menentukan skor tempat ini.</p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        @foreach ($checklist as $item)
                            <div class="flex items-center gap-3.5 rounded-2xl border-2 p-4 {{ $item['available'] ? 'border-go-500/25 bg-go-50' : 'border-ink-900/8 bg-paper-dim' }}">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl {{ $item['available'] ? 'bg-go-600 text-white' : 'bg-ink-900/8 text-ink-400' }}">
                                    <x-pictogram :name="$item['icon'] ?? 'check'" class="h-6 w-6" :stroke="2.2" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-extrabold text-ink-900">{{ $item['label'] }}</p>
                                    <p class="text-xs font-bold {{ $item['available'] ? 'text-go-700' : 'text-ink-400' }}">
                                        {{ $item['available'] ? 'Tersedia' : 'Tidak tersedia' }}
                                    </p>
                                </div>
                                @if ($item['available'])
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-go-600 text-white" aria-label="Tersedia">
                                        <x-pictogram name="check" class="h-4 w-4" :stroke="2.6" />
                                    </span>
                                @else
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-ink-900/10 text-ink-500" aria-label="Tidak tersedia">
                                        <x-pictogram name="blocked" class="h-4 w-4" :stroke="2.4" />
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if ($place->features?->notes)
                        <div class="mt-6 flex gap-3.5 rounded-2xl bg-warn-50 p-4 ring-2 ring-inset ring-warn-500/30">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-warn-500 text-board-900">
                                <x-pictogram name="warning" class="h-6 w-6" :stroke="2.2" />
                            </span>
                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-wide text-warn-700">Catatan lapangan</p>
                                <p class="mt-1 text-sm text-ink-700">{{ $place->features->notes }}</p>
                            </div>
                        </div>
                    @endif
                </section>

                {{-- Location map --}}
                <section aria-labelledby="location-heading" class="mt-6 overflow-hidden rounded-3xl border-2 border-ink-900/5 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b-2 border-ink-900/5 bg-paper-dim px-5 py-3.5">
                        <h2 id="location-heading" class="flex items-center gap-2 font-display text-sm font-extrabold uppercase tracking-wide text-ink-900">
                            <x-pictogram name="locate" class="h-4.5 w-4.5 text-route-cyan" /> Lokasi di peta
                        </h2>
                        <a href="https://www.openstreetmap.org/?mlat={{ $place->latitude }}&mlon={{ $place->longitude }}#map=17/{{ $place->latitude }}/{{ $place->longitude }}"
                           target="_blank" rel="noopener"
                           class="flex h-11 items-center gap-2 rounded-xl bg-board-900 px-4 text-xs font-extrabold text-white transition hover:bg-board-800">
                            Buka rute
                            <x-pictogram name="arrow" class="h-3.5 w-3.5" />
                        </a>
                    </div>
                    <div id="place-map" class="h-[340px] w-full"></div>
                </section>

                @auth
                    @if (auth()->user()->isUser())
                        <a href="{{ route('user.report.create') }}?place_id={{ $place->id }}"
                           class="mt-6 flex h-14 items-center justify-center gap-2.5 rounded-2xl border-2 border-ink-900/10 bg-white text-base font-extrabold text-ink-900 transition hover:border-warn-500 hover:text-warn-700">
                            <x-pictogram name="report" class="h-5 w-5" />
                            Laporkan perubahan kondisi tempat ini
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Right: the score board --}}
            <div class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                <div class="overflow-hidden rounded-3xl border-2 border-ink-900/5 bg-white shadow-lg">
                    <div class="{{ $region['field'] }} relative p-8 text-center text-white">
                        <div class="pointer-events-none absolute inset-0 opacity-[0.1]" aria-hidden="true"
                             style="background-image: radial-gradient(#fff 2px, transparent 2.4px); background-size: 18px 18px;"></div>
                        <p class="relative text-xs font-extrabold uppercase tracking-widest text-white/90">Skor aksesibilitas</p>
                        <div class="relative mt-4 flex items-center justify-center gap-1.5" aria-label="Skor {{ $place->overall_score }} dari 100">
                            @foreach (str_split(str_pad((string) $place->overall_score, 2, '0', STR_PAD_LEFT)) as $digit)
                                <span class="flap !bg-board-950 text-5xl">{{ $digit }}</span>
                            @endforeach
                            <span class="ml-1 font-display text-xl font-extrabold text-white/85">/100</span>
                        </div>
                        <div class="relative mx-auto mt-5 h-2.5 w-full max-w-xs overflow-hidden rounded-full bg-white/25">
                            <div class="h-full rounded-full bg-white" style="width: {{ $place->overall_score }}%"></div>
                        </div>
                        <p class="relative mt-4 flex items-center justify-center gap-2 text-sm font-extrabold">
                            <x-pictogram name="check" class="h-4 w-4" /> {{ $place->statusLabel() }}
                        </p>
                    </div>

                    <div class="p-6 text-ink-900">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-ink-400">Ditinjau oleh</p>
                        <p class="mt-1 text-sm font-bold text-ink-900">{{ $place->creator?->name ?? 'Kontributor AksesKita' }}</p>
                        <p class="mt-4 border-t-2 border-dashed border-ink-900/10 pt-4 text-xs font-semibold text-ink-500">
                            Diperbarui {{ $place->updated_at->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>

                @if ($related->isNotEmpty())
                    <section aria-labelledby="related-heading">
                        <h2 id="related-heading" class="font-display text-sm font-extrabold uppercase tracking-wide text-white/60">Tempat serupa</h2>
                        <div class="mt-3 space-y-3">
                            @foreach ($related as $item)
                                <a href="{{ route('place.show', $item->slug) }}" class="flex items-center gap-3.5 rounded-2xl border border-white/10 bg-white/[0.05] p-3.5 transition hover:border-route-cyan/50 hover:bg-white/10">
                                    <span class="flap h-12 !min-w-12 text-lg" style="background: {{ $item->statusHex() }}">{{ $item->overall_score }}</span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-extrabold text-white">{{ $item->name }}</p>
                                        <p class="truncate text-xs font-semibold text-white/55">{{ $item->categoryLabel() }}</p>
                                    </div>
                                    <x-pictogram name="arrow" class="ml-auto h-4 w-4 shrink-0 text-white/40" />
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('place-map');
        if (!el || typeof window.L === 'undefined') return;

        const lat = {{ (float) $place->latitude }};
        const lng = {{ (float) $place->longitude }};

        const map = L.map(el, { center: [lat, lng], zoom: 16 });
        window.AksesKitaMap.baseTiles().addTo(map);
        window.AksesKitaMap.addPlaces(map, [@js($place->toMapArray())]);
    });
</script>
@endpush
