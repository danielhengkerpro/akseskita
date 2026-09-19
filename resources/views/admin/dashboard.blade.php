@extends('layouts.dashboard')

@section('title', 'Dashboard Admin — AksesKita')
@section('heading', 'Ringkasan Sistem')
@section('subheading', 'Statistik data tempat, laporan warga, dan status moderasi.')

@section('content')
{{-- Statistik utama --}}
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @php
        $cards = [
            ['label' => 'Total Tempat', 'value' => $stats['places'], 'tone' => 'text-ink-900', 'bg' => 'bg-white'],
            ['label' => 'Terverifikasi', 'value' => $stats['verified'], 'tone' => 'text-route-cyan-ink', 'bg' => 'bg-brand-50'],
            ['label' => 'Laporan Menunggu', 'value' => $stats['reports_pending'], 'tone' => 'text-warn-700', 'bg' => 'bg-warn-50'],
            ['label' => 'Total Kontributor', 'value' => $stats['users'], 'tone' => 'text-ink-900', 'bg' => 'bg-white'],
        ];
    @endphp
    @foreach ($cards as $card)
        <div class="rounded-2xl border border-ink-900/5 {{ $card['bg'] }} p-5 shadow-sm">
            <p class="text-3xl font-extrabold {{ $card['tone'] }}">{{ $card['value'] }}</p>
            <p class="mt-1 text-xs font-semibold text-ink-900/50">{{ $card['label'] }}</p>
        </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    {{-- Distribusi warna --}}
    <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm lg:col-span-1">
        <h2 class="font-bold text-ink-900">Distribusi Kelayakan</h2>
        <p class="text-sm text-ink-900/50">Sebaran status aksesibilitas.</p>

        @php
            $total = max($stats['places'], 1);
            $bars = [
                ['label' => 'Ramah', 'value' => $stats['green'], 'color' => 'bg-go-500', 'shape' => '<span class="inline-block h-2.5 w-2.5 rotate-45 bg-go-500" aria-hidden="true"></span>'],
                ['label' => 'Terbatas', 'value' => $stats['yellow'], 'color' => 'bg-warn-500', 'shape' => '<span class="inline-block h-0 w-0 border-x-[5px] border-b-[9px] border-x-transparent border-b-warn-500" aria-hidden="true"></span>'],
                ['label' => 'Berbahaya', 'value' => $stats['red'], 'color' => 'bg-stop-500', 'shape' => '<span class="inline-block h-2.5 w-2.5 rounded-full bg-stop-500" aria-hidden="true"></span>'],
            ];
        @endphp

        <div class="mt-5 space-y-4">
            @foreach ($bars as $bar)
                <div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 font-bold text-ink-700">{!! $bar['shape'] !!} {{ $bar['label'] }}</span>
                        <span class="font-extrabold text-ink-900 text-tabular">{{ $bar['value'] }}</span>
                    </div>
                    <div class="mt-1.5 h-2.5 overflow-hidden rounded-full bg-ink-900/5">
                        <div class="{{ $bar['color'] }} h-full rounded-full transition-all duration-700" style="width: {{ round($bar['value'] / $total * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 border-t border-ink-900/5 pt-5">
            <h3 class="text-sm font-bold text-ink-900">Per Kategori</h3>
            <div class="mt-3 space-y-2">
                @foreach (\App\Models\Place::CATEGORIES as $key => $label)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-ink-900/60">{{ $label }}</span>
                        <span class="font-semibold text-ink-900">{{ $categoryBreakdown[$key] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Laporan terbaru --}}
    <div class="rounded-2xl border border-ink-900/5 bg-white shadow-sm lg:col-span-2">
        <div class="flex items-center justify-between border-b border-ink-900/5 px-6 py-4">
            <div>
                <h2 class="font-display font-extrabold text-ink-900">Laporan Terbaru</h2>
                <p class="text-sm text-ink-900/50">Perlu ditinjau oleh admin.</p>
            </div>
            <a href="{{ route('admin.moderation.index') }}" class="flex h-11 items-center gap-2 rounded-xl bg-board-900 px-4 text-sm font-extrabold text-white transition hover:bg-board-800">
                <x-pictogram name="check" class="h-4 w-4" /> Moderasi
            </a>
        </div>

        @if ($latestReports->isEmpty())
            <div class="px-6 py-14 text-center">
                <span class="text-5xl">✨</span>
                <p class="mt-3 font-semibold text-ink-900">Belum ada laporan masuk</p>
            </div>
        @else
            <div class="divide-y divide-ink-900/5">
                @foreach ($latestReports as $report)
                    <div class="flex items-center gap-4 px-6 py-4">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-route-cyan/12 text-sm font-extrabold text-route-cyan-ink">
                            {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-ink-900">{{ $report->place_name }}</p>
                            <p class="truncate text-xs text-ink-900/50">oleh {{ $report->user->name ?? 'Pengguna' }} · {{ $report->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $report->statusColorClasses() }}">
                            {{ $report->statusLabel() }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
