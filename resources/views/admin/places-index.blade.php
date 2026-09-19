@extends('layouts.dashboard')

@section('title', 'Kelola Tempat — AksesKita')
@section('heading', 'Kelola Data Tempat')
@section('subheading', 'Tambah, ubah, atau hapus data tempat & fasilitas aksesibilitas.')

@section('content')
<div class="flex flex-wrap items-center gap-3">
    <form method="GET" class="flex flex-1 flex-wrap items-center gap-2">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama tempat…"
               class="w-48 rounded-xl border-0 bg-white px-4 py-2.5 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan sm:w-64">
        <div class="relative" x-data="{ open: false, selected: '{{ request('category', 'all') }}', label: '{{ request('category') && isset($categories[request('category')]) ? $categories[request('category')] : 'Semua kategori' }}' }" @click.outside="open = false" @keydown.escape.window="open = false">
            <input type="hidden" name="category" :value="selected">
            <button type="button" @click="open = !open"
                    class="flex h-10 items-center justify-between gap-3 rounded-xl border-0 bg-white px-4 text-sm font-semibold text-ink-900 ring-1 ring-inset ring-ink-900/10 focus:ring-2 focus:ring-route-cyan">
                <span x-text="label"></span>
                <svg class="h-4 w-4 text-ink-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open"
                 x-cloak
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute left-0 top-full z-50 mt-1 w-56 rounded-xl border border-ink-900/10 bg-white p-1.5 shadow-xl">
                <button type="button"
                        @click="selected = 'all'; label = 'Semua kategori'; open = false; $el.closest('form').submit()"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold text-ink-900 hover:bg-paper-dim transition">
                    <span>Semua kategori</span>
                    <span x-show="selected === 'all'" class="text-route-cyan font-bold">✔</span>
                </button>
                @foreach ($categories as $key => $l)
                    <button type="button"
                            @click="selected = '{{ $key }}'; label = '{{ $l }}'; open = false; $el.closest('form').submit()"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold text-ink-900 hover:bg-paper-dim transition">
                        <span>{{ $l }}</span>
                        <span x-show="selected === '{{ $key }}'" class="text-route-cyan font-bold">✔</span>
                    </button>
                @endforeach
            </div>
        </div>
        <button type="submit" class="rounded-xl bg-ink-900 px-5 py-2.5 text-sm font-bold text-white">Filter</button>
    </form>

    <a href="{{ route('admin.places.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-board-900 px-5 text-sm font-extrabold text-white transition hover:bg-board-800">
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
        Tambah Tempat
    </a>
</div>

<div class="mt-6 overflow-hidden rounded-2xl border border-ink-900/5 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-900/5 bg-paper-dim text-xs uppercase tracking-wide text-ink-900/50">
                <tr>
                    <th class="px-6 py-3.5 font-bold">Tempat</th>
                    <th class="px-4 py-3.5 font-bold">Kategori</th>
                    <th class="px-4 py-3.5 font-bold">Skor</th>
                    <th class="px-4 py-3.5 font-bold">Status</th>
                    <th class="px-4 py-3.5 font-bold">Verifikasi</th>
                    <th class="px-6 py-3.5 text-right font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-900/5">
                @forelse ($places as $place)
                    <tr class="transition hover:bg-paper-dim">
                        <td class="px-6 py-4">
                            <p class="font-bold text-ink-900">{{ $place->name }}</p>
                            <p class="line-clamp-1 text-xs text-ink-900/50">{{ $place->address }}</p>
                        </td>
                        <td class="px-4 py-4 text-ink-900/70">{{ $place->categoryLabel() }}</td>
                        <td class="px-4 py-4">
                            <span class="font-extrabold text-ink-900">{{ $place->overall_score }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <x-status-badge :color="$place->status_color" size="sm" />
                        </td>
                        <td class="px-4 py-4">
                            @if ($place->is_verified)
                                <span class="text-xs font-bold text-brand-700">✓ Ya</span>
                            @else
                                <span class="text-xs font-bold text-ink-900/40">Belum</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('place.show', $place->slug) }}" target="_blank" class="rounded-lg px-3 py-1.5 text-xs font-bold text-ink-900/60 hover:bg-ink-900/5">Lihat</a>
                                <a href="{{ route('admin.places.edit', $place) }}" class="rounded-lg px-3 py-1.5 text-xs font-bold text-route-cyan-ink hover:bg-brand-50">Edit</a>
                                <form method="POST" action="{{ route('admin.places.destroy', $place) }}" onsubmit="return confirm('Hapus tempat &quot;{{ $place->name }}&quot;?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-bold text-stop-600 hover:bg-stop-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-ink-900/50">Belum ada tempat terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($places->hasPages())
        <div class="border-t border-ink-900/5 px-6 py-4">
            {{ $places->links() }}
        </div>
    @endif
</div>
@endsection
