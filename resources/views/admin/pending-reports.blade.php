@extends('layouts.dashboard')

@section('title', 'Moderasi Laporan — AksesKita')
@section('heading', 'Moderasi Laporan Warga')
@section('subheading', 'Tinjau, setujui, atau tolak laporan fasilitas dari kontributor.')

@section('content')
{{-- Tab filter status --}}
<div class="flex flex-wrap gap-2">
    @php
        $tabs = [
            'pending' => ['label' => 'Menunggu', 'count' => $counts['pending'], 'dot' => 'bg-warn-500'],
            'approved' => ['label' => 'Disetujui', 'count' => $counts['approved'], 'dot' => 'bg-go-500'],
            'rejected' => ['label' => 'Ditolak', 'count' => $counts['rejected'], 'dot' => 'bg-stop-500'],
            'all' => ['label' => 'Semua', 'count' => $counts['all'], 'dot' => 'bg-board-900'],
        ];
    @endphp
    @foreach ($tabs as $key => $tab)
        <a href="{{ route('admin.moderation.index', ['status' => $key]) }}"
           @if ($status === $key) aria-current="page" @endif
           class="flex h-11 items-center gap-2 rounded-xl px-4 text-sm font-extrabold transition {{ $status === $key ? 'bg-board-900 text-white' : 'bg-white text-ink-600 ring-1 ring-inset ring-ink-900/10 hover:ring-ink-900/25' }}">
            <span class="h-2.5 w-2.5 rounded-full {{ $tab['dot'] }}" aria-hidden="true"></span>
            {{ $tab['label'] }}
            <span class="rounded-md bg-black/10 px-2 py-0.5 text-xs text-tabular">{{ $tab['count'] }}</span>
        </a>
    @endforeach
</div>

<div class="mt-6 space-y-4">
    @forelse ($reports as $report)
        <div class="overflow-hidden rounded-2xl border-2 border-ink-900/8 bg-white shadow-sm">
            <div class="flex flex-col gap-5 p-6 lg:flex-row">
                {{-- Foto --}}
                @if ($report->photoUrl())
                    <img src="{{ $report->photoUrl() }}" alt="Foto bukti laporan {{ $report->place_name }}" class="h-40 w-full rounded-xl object-cover ring-2 ring-ink-900/8 lg:h-32 lg:w-44">
                @else
                    <div class="grid h-40 w-full place-items-center rounded-xl bg-paper-dim text-ink-400 lg:h-32 lg:w-44">
                        <x-pictogram name="report" class="h-9 w-9" />
                    </div>
                @endif

                {{-- Konten --}}
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="font-display text-lg font-extrabold text-ink-900">{{ $report->place_name }}</h3>
                        <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-extrabold uppercase tracking-wide ring-1 ring-inset {{ $report->statusColorClasses() }}">
                            {{ $report->statusLabel() }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm leading-relaxed text-ink-600">{{ $report->description }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs font-semibold text-ink-500">
                        <span class="inline-flex items-center gap-1.5"><x-pictogram name="community" class="h-3.5 w-3.5" /> {{ $report->user->name ?? 'Pengguna' }} ({{ $report->user->email ?? '-' }})</span>
                        <span class="inline-flex items-center gap-1.5"><x-pictogram name="info" class="h-3.5 w-3.5" /> {{ $report->created_at->translatedFormat('d M Y, H:i') }}</span>
                        @if ($report->place)
                            <a href="{{ route('place.show', $report->place->slug) }}" class="inline-flex items-center gap-1.5 font-bold text-route-cyan-ink hover:underline"><x-pictogram name="pin" class="h-3.5 w-3.5" /> {{ $report->place->name }}</a>
                        @endif
                        @if ($report->latitude && $report->longitude)
                            <span class="inline-flex items-center gap-1.5"><x-pictogram name="locate" class="h-3.5 w-3.5" /> {{ $report->latitude }}, {{ $report->longitude }}</span>
                        @endif
                        @if ($report->reviewer)
                            <span class="inline-flex items-center gap-1.5"><x-pictogram name="check" class="h-3.5 w-3.5" /> Ditinjau oleh {{ $report->reviewer->name }}</span>
                        @endif
                    </div>
                </div>

                {{-- Aksi --}}
                @if ($report->isPending())
                    <div class="flex shrink-0 gap-2 lg:flex-col">
                        <form method="POST" action="{{ route('admin.moderation.approve', $report) }}">
                            @csrf
                            <button type="submit" class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-go-700 px-5 text-sm font-extrabold text-white transition hover:bg-go-600">
                                <x-pictogram name="check" class="h-4 w-4" /> Setujui
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.moderation.reject', $report) }}">
                            @csrf
                            <button type="submit" class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-stop-600 px-5 text-sm font-extrabold text-white transition hover:bg-stop-700">
                                <x-pictogram name="blocked" class="h-4 w-4" /> Tolak
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="rounded-2xl border-2 border-ink-900/8 bg-white px-6 py-16 text-center shadow-sm">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-paper-dim text-ink-400">
                <x-pictogram name="check" class="h-8 w-8" />
            </span>
            <p class="mt-4 font-display font-extrabold text-ink-900">Tidak ada laporan pada kategori ini</p>
            <p class="mt-1 text-sm font-semibold text-ink-500">Semua sudah tertangani. Kerja bagus!</p>
        </div>
    @endforelse
</div>

@if ($reports->hasPages())
    <div class="mt-6">
        {{ $reports->links() }}
    </div>
@endif
@endsection
