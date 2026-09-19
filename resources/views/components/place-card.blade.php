@props(['place'])

@php
    // Colour owns the whole header region — the region IS the status colour.
    $region = match ($place->status_color) {
        'green' => ['bar' => 'bg-go-700', 'field' => 'bg-go-700', 'text' => 'text-white'],
        'red' => ['bar' => 'bg-stop-700', 'field' => 'bg-stop-700', 'text' => 'text-white'],
        default => ['bar' => 'bg-warn-600', 'field' => 'bg-warn-500', 'text' => 'text-board-950'],
    };

    $categoryIcons = [
        'stasiun' => 'ramp',
        'mal' => 'door',
        'rumah_sakit' => 'toilet',
        'kafe' => 'star',
        'taman' => 'guiding-block',
        'fasilitas_publik' => 'community',
    ];
    $icon = $categoryIcons[$place->category] ?? 'pin';

    // Six features, compact — reads like a mini checklist on the sign.
    $featureRow = [];

    $shape = match ($place->status_color) {
        'green' => '<span class="inline-block h-2.5 w-2.5 rotate-45 bg-current" aria-hidden="true"></span>',
        'red' => '<span class="inline-block h-2.5 w-2.5 rounded-full bg-current" aria-hidden="true"></span>',
        default => '<span class="inline-block h-0 w-0 border-x-[5px] border-b-[9px] border-x-transparent border-b-current" aria-hidden="true"></span>',
    };
@endphp

<a href="{{ route('place.show', $place->slug) }}"
   class="group card-edge relative flex h-full flex-col overflow-hidden rounded-3xl bg-white shadow-sm transition duration-500 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-board-900/10 focus-visible:-translate-y-1.5">

    {{-- Coloured field header: status owns the region --}}
    <div class="relative overflow-hidden {{ $region['field'] }} p-5">
        <div class="pointer-events-none absolute inset-0 opacity-[0.12] {{ $region['text'] }}"
             style="background-image: radial-gradient(currentColor 2px, transparent 2.4px); background-size: 16px 16px;" aria-hidden="true"></div>
        <div class="relative flex items-start justify-between gap-3 {{ $region['text'] }}">
            <div class="flex items-center gap-3">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/20 backdrop-blur-sm {{ $region['text'] }}">
                    <x-pictogram :name="$icon" class="h-6 w-6" :stroke="2.2" />
                </span>
                <div>
                    <p class="text-[11px] font-extrabold uppercase tracking-widest {{ $region['text'] }}">{{ $place->categoryLabel() }}</p>
                    <x-status-badge :color="$place->status_color" size="sm" class="mt-1 !bg-white" />
                </div>
            </div>
        </div>

        <h3 class="relative mt-5 font-display text-xl font-extrabold leading-snug {{ $region['text'] }}">{{ $place->name }}</h3>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <p class="flex items-start gap-2 text-sm leading-relaxed text-ink-500">
            <x-pictogram name="pin" class="mt-0.5 h-4 w-4 shrink-0 text-ink-400" />
            <span class="line-clamp-2">{{ $place->address }}</span>
        </p>

        {{-- Score readout: split-flap style --}}
        <div class="mt-5 flex items-end justify-between border-t-2 border-dashed border-ink-900/10 pt-5">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-widest text-ink-400">Skor akses</p>
                <div class="mt-1.5 flex items-center gap-1" aria-label="Skor {{ $place->overall_score }} dari 100">
                    @foreach (str_split(str_pad((string) $place->overall_score, 2, '0', STR_PAD_LEFT)) as $digit)
                        <span class="flap text-base {{ $region['bar'] }} {{ $region['text'] }}">{{ $digit }}</span>
                    @endforeach
                    <span class="ml-0.5 text-xs font-bold text-ink-400">/100</span>
                </div>
            </div>

            <div class="text-right">
                @if ($place->is_verified)
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-go-50 px-2.5 py-1.5 text-[11px] font-extrabold text-go-700 ring-1 ring-inset ring-go-600/20">
                        <x-pictogram name="check" class="h-3.5 w-3.5" /> Terverifikasi
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-paper-dim px-2.5 py-1.5 text-[11px] font-extrabold text-ink-400 ring-1 ring-inset ring-ink-900/10">
                        <x-pictogram name="info" class="h-3.5 w-3.5" /> Menunggu
                    </span>
                @endif
                <span class="mt-2 flex items-center justify-end gap-1 text-sm font-extrabold text-route-cyan-ink">
                    Detail
                    <x-pictogram name="arrow" class="h-4 w-4 transition group-hover:translate-x-1" />
                </span>
            </div>
        </div>
    </div>
</a>
