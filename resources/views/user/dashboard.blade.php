@extends('layouts.dashboard')

@section('title', 'Dashboard — AksesKita')
@section('heading', 'Beranda Saya')
@section('subheading', 'Ringkasan kontribusi, status verifikasi, dan aksi cepat.')

@section('content')
<div x-data="dashboardState()" class="space-y-6">

    {{-- ═══ Personalization Banner (Disability Account) ═══ --}}
    @if ($user->isDisabilityAccount() && $disabilityInfo)
        <div class="relative overflow-hidden rounded-2xl border border-route-cyan/20 bg-gradient-to-br from-route-cyan/5 via-white to-route-cyan/10 p-5 sm:p-6">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-route-cyan/5 blur-2xl"></div>
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2.5">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-route-cyan/15 text-xl">
                            {{ match($user->disability_type) {
                                'wheelchair' => '♿',
                                'blind' => '🦯',
                                'deaf' => '🧏',
                                'elderly' => '🚶',
                                default => '🧩',
                            } }}
                        </span>
                        <div>
                            <p class="font-display text-base font-extrabold text-ink-900">
                                Halo, {{ explode(' ', $user->name)[0] }}! 👋
                            </p>
                            <p class="text-xs font-semibold text-ink-500">
                                Dashboard ini disesuaikan untuk kebutuhan <strong class="text-route-cyan-ink">{{ $disabilityInfo['label'] }}</strong>
                            </p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs font-semibold text-ink-500 leading-relaxed">
                        {{ $disabilityInfo['description'] }}
                    </p>
                </div>
                <div class="flex shrink-0 flex-wrap gap-2">
                    <a href="{{ route('map.index') }}"
                       class="inline-flex h-10 items-center gap-2 rounded-xl bg-route-cyan px-4 text-xs font-extrabold text-white shadow-md shadow-route-cyan/20 transition hover:bg-route-cyan-ink">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.157 2.176a1.5 1.5 0 011.38-.176l3.927 1.59 3.869-1.29a1.5 1.5 0 012.167 1.342v11.716a1.5 1.5 0 01-.967 1.401l-4.313 1.438a1.5 1.5 0 01-1.057-.05L9.31 16.64l-3.843 1.28A1.5 1.5 0 013.3 16.58V4.864a1.5 1.5 0 01.967-1.401l3.89-1.287z" clip-rule="evenodd"/></svg>
                        Peta Sesuai Kebutuhan Saya
                    </a>
                    <button @click="showPrefsModal = true"
                            class="inline-flex h-10 items-center gap-2 rounded-xl border border-ink-900/10 bg-white px-4 text-xs font-bold text-ink-700 transition hover:border-route-cyan hover:text-route-cyan-ink">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.34 1.804A1 1 0 019.32 1h1.36a1 1 0 01.98.804l.295 1.473c.497.144.971.342 1.416.587l1.25-.834a1 1 0 011.262.125l.962.962a1 1 0 01.125 1.262l-.834 1.25c.245.445.443.919.587 1.416l1.473.295a1 1 0 01.804.98v1.361a1 1 0 01-.804.98l-1.473.295a6.95 6.95 0 01-.587 1.416l.834 1.25a1 1 0 01-.125 1.262l-.962.962a1 1 0 01-1.262.125l-1.25-.834a6.953 6.953 0 01-1.416.587l-.295 1.473a1 1 0 01-.98.804H9.32a1 1 0 01-.98-.804l-.295-1.473a6.957 6.957 0 01-1.416-.587l-1.25.834a1 1 0 01-1.262-.125l-.962-.962a1 1 0 01-.125-1.262l.834-1.25a6.957 6.957 0 01-.587-1.416l-1.473-.295A1 1 0 011 11.36V9.999a1 1 0 01.804-.98l1.473-.295c.144-.497.342-.971.587-1.416l-.834-1.25a1 1 0 01.125-1.262l.962-.962A1 1 0 015.38 3.71l1.25.834a6.957 6.957 0 011.416-.587l.295-1.473zM13 10a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        Sesuaikan
                    </button>
                </div>
            </div>
        </div>
    @else
        {{-- Generic welcome for non-disability accounts --}}
        <div class="rounded-2xl border border-go-500/20 bg-gradient-to-br from-go-50 via-white to-go-50/50 p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-display text-base font-extrabold text-ink-900">Selamat datang, {{ explode(' ', $user->name)[0] }}! 👋</p>
                    <p class="mt-0.5 text-xs font-semibold text-ink-500">Sebagai Relawan/Kontributor, kamu bisa melaporkan fasilitas dan mengajukan tempat baru di kota kamu.</p>
                </div>
                <a href="{{ route('map.index') }}"
                   class="inline-flex h-10 items-center gap-2 rounded-xl bg-go-700 px-4 text-xs font-extrabold text-white shadow-md shadow-go-700/20 transition hover:bg-go-600">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.157 2.176a1.5 1.5 0 011.38-.176l3.927 1.59 3.869-1.29a1.5 1.5 0 012.167 1.342v11.716a1.5 1.5 0 01-.967 1.401l-4.313 1.438a1.5 1.5 0 01-1.057-.05L9.31 16.64l-3.843 1.28A1.5 1.5 0 013.3 16.58V4.864a1.5 1.5 0 01.967-1.401l3.89-1.287z" clip-rule="evenodd"/></svg>
                    Buka Peta Publik
                </a>
            </div>
        </div>
    @endif

    {{-- ═══ Summary Statistics Cards ═══ --}}
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
        @php
            $statCards = [
                ['label' => 'Total Laporan',     'value' => $reportSummary['total'],    'icon' => '📋', 'bg' => 'bg-white',    'tone' => 'text-ink-900'],
                ['label' => 'Menunggu Verifikasi','value' => $reportSummary['pending'],  'icon' => '⏳', 'bg' => 'bg-warn-50',  'tone' => 'text-warn-700'],
                ['label' => 'Disetujui',          'value' => $reportSummary['approved'], 'icon' => '✅', 'bg' => 'bg-go-50',    'tone' => 'text-go-700'],
                ['label' => 'Ditolak',            'value' => $reportSummary['rejected'], 'icon' => '❌', 'bg' => 'bg-stop-50',  'tone' => 'text-stop-700'],
                ['label' => 'Tempat Diajukan',    'value' => $placeSummary['total'],     'icon' => '📍', 'bg' => 'bg-route-cyan/5', 'tone' => 'text-route-cyan-ink'],
            ];
        @endphp
        @foreach ($statCards as $card)
            <div class="rounded-2xl border border-ink-900/5 {{ $card['bg'] }} p-4 shadow-sm transition hover:shadow-md">
                <div class="flex items-center gap-2.5">
                    <span class="text-lg">{{ $card['icon'] }}</span>
                    <p class="text-2xl font-extrabold {{ $card['tone'] }}">{{ $card['value'] }}</p>
                </div>
                <p class="mt-1.5 text-[11px] font-bold uppercase tracking-wider text-ink-500">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ═══ Quick Action Cards ═══ --}}
    <div class="grid gap-4 sm:grid-cols-2">
        {{-- Buat Laporan --}}
        <a href="{{ route('user.report.create') }}"
           class="group relative overflow-hidden rounded-2xl border border-ink-900/5 bg-white p-5 shadow-sm transition hover:border-go-300 hover:shadow-lg">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-go-100/50 transition group-hover:scale-125"></div>
            <div class="relative">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-go-100 text-xl transition group-hover:bg-go-200">📝</span>
                <h3 class="mt-3 font-display text-sm font-extrabold text-ink-900">Buat Laporan Fasilitas</h3>
                <p class="mt-1 text-xs font-semibold text-ink-500 leading-relaxed">
                    Laporkan kondisi ramp, lift, ubin pemandu, atau fasilitas aksesibilitas lainnya yang kamu temui.
                </p>
                <span class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-go-700 transition group-hover:gap-2">
                    Mulai Lapor
                    <svg class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </span>
            </div>
        </a>

        {{-- Ajukan Tempat Baru --}}
        <a href="{{ route('user.places.create') }}"
           class="group relative overflow-hidden rounded-2xl border border-ink-900/5 bg-white p-5 shadow-sm transition hover:border-route-cyan/40 hover:shadow-lg">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-route-cyan/10 transition group-hover:scale-125"></div>
            <div class="relative">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-route-cyan/10 text-xl transition group-hover:bg-route-cyan/20">📍</span>
                <h3 class="mt-3 font-display text-sm font-extrabold text-ink-900">Ajukan Tempat Baru</h3>
                <p class="mt-1 text-xs font-semibold text-ink-500 leading-relaxed">
                    Daftarkan gedung, stasiun, taman, atau fasilitas publik baru yang belum tercatat di peta AksesKita.
                </p>
                <span class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-route-cyan-ink transition group-hover:gap-2">
                    Ajukan Sekarang
                    <svg class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </span>
            </div>
        </a>
    </div>

    {{-- ═══ CTA Green Places ═══ --}}
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border-2 border-go-500/25 bg-go-50/70 p-5">
        <div>
            <p class="font-display text-sm font-extrabold text-go-700">🌿 {{ $greenPlaces }} tempat kini berstatus ramah aksesibilitas</p>
            <p class="mt-0.5 text-xs font-semibold text-ink-600">Bantu kami menambah data aksesibilitas di kota kamu!</p>
        </div>
        <a href="{{ route('map.index') }}" class="flex h-10 items-center gap-2 rounded-xl bg-go-700 px-4 text-xs font-extrabold text-white transition hover:bg-go-600">
            Lihat di Peta
        </a>
    </div>

    {{-- ═══ Tabbed Content: Laporan & Tempat ═══ --}}
    <div class="overflow-hidden rounded-2xl border border-ink-900/5 bg-white shadow-sm">
        {{-- Tab Navigation --}}
        <div class="flex border-b border-ink-900/5">
            <button @click="activeTab = 'reports'" :class="activeTab === 'reports' ? 'border-route-cyan text-route-cyan-ink' : 'border-transparent text-ink-500 hover:text-ink-700'"
                    class="flex-1 border-b-2 px-4 py-3.5 text-xs font-bold uppercase tracking-wider transition sm:flex-none sm:px-6">
                📋 Laporan Saya <span class="ml-1 rounded-full bg-ink-100 px-2 py-0.5 text-[10px] font-extrabold text-ink-600" x-text="'{{ $reportSummary['total'] }}'"></span>
            </button>
            <button @click="activeTab = 'places'" :class="activeTab === 'places' ? 'border-route-cyan text-route-cyan-ink' : 'border-transparent text-ink-500 hover:text-ink-700'"
                    class="flex-1 border-b-2 px-4 py-3.5 text-xs font-bold uppercase tracking-wider transition sm:flex-none sm:px-6">
                📍 Tempat Diajukan <span class="ml-1 rounded-full bg-ink-100 px-2 py-0.5 text-[10px] font-extrabold text-ink-600" x-text="'{{ $placeSummary['total'] }}'"></span>
            </button>
        </div>

        {{-- Tab 1: Laporan --}}
        <div x-show="activeTab === 'reports'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            @if ($reports->isEmpty())
                <div class="px-6 py-16 text-center">
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-paper-dim text-3xl">📋</span>
                    <p class="mt-4 font-display text-sm font-extrabold text-ink-900">Belum ada laporan</p>
                    <p class="mt-1 text-xs font-semibold text-ink-500">Mulai laporkan fasilitas aksesibilitas di sekitarmu.</p>
                    <a href="{{ route('user.report.create') }}" class="mt-5 inline-flex h-11 items-center gap-2 rounded-xl bg-go-700 px-5 text-xs font-extrabold text-white transition hover:bg-go-600">
                        📝 Buat Laporan Pertama
                    </a>
                </div>
            @else
                <div class="divide-y divide-ink-900/5">
                    @foreach ($reports as $report)
                        <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center">
                            @if ($report->photoUrl())
                                <img src="{{ $report->photoUrl() }}" alt="Foto laporan" class="h-14 w-14 shrink-0 rounded-xl object-cover ring-1 ring-ink-900/5">
                            @else
                                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-paper-dim text-xl">📋</span>
                            @endif

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-ink-900">{{ $report->place_name }}</p>
                                <p class="mt-0.5 line-clamp-1 text-xs text-ink-500">{{ $report->description }}</p>
                                <p class="mt-1 text-[11px] text-ink-400">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold ring-1 ring-inset {{ $report->statusColorClasses() }}">
                                    {{ $report->statusLabel() }}
                                </span>
                                @if ($report->place)
                                    <a href="{{ route('place.show', $report->place->slug) }}" class="text-xs font-bold text-route-cyan-ink hover:underline">Lihat →</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($reports->hasPages())
                    <div class="border-t border-ink-900/5 px-6 py-4">
                        {{ $reports->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Tab 2: Tempat yang Diajukan --}}
        <div x-show="activeTab === 'places'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            @if ($userPlaces->isEmpty())
                <div class="px-6 py-16 text-center">
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-paper-dim text-3xl">📍</span>
                    <p class="mt-4 font-display text-sm font-extrabold text-ink-900">Belum ada tempat yang diajukan</p>
                    <p class="mt-1 text-xs font-semibold text-ink-500">Bantu lengkapi peta AksesKita dengan mengajukan tempat baru.</p>
                    <a href="{{ route('user.places.create') }}" class="mt-5 inline-flex h-11 items-center gap-2 rounded-xl bg-route-cyan px-5 text-xs font-extrabold text-white transition hover:bg-route-cyan-ink">
                        📍 Ajukan Tempat Pertama
                    </a>
                </div>
            @else
                <div class="divide-y divide-ink-900/5">
                    @foreach ($userPlaces as $place)
                        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl text-xl {{ $place->is_verified ? 'bg-go-50' : 'bg-warn-50' }}">
                                {{ $place->is_verified ? '✅' : '⏳' }}
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-ink-900">{{ $place->name }}</p>
                                <p class="mt-0.5 text-xs text-ink-500">{{ $place->categoryLabel() }} · {{ $place->address }}</p>
                                <p class="mt-1 text-[11px] text-ink-400">Diajukan {{ $place->created_at->translatedFormat('d M Y') }}</p>
                            </div>

                            <div class="flex items-center gap-3">
                                @if ($place->is_verified)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-go-50 px-3 py-1 text-[11px] font-bold text-go-700 ring-1 ring-inset ring-go-600/25">
                                        ✅ Terverifikasi & Tayang
                                    </span>
                                    <a href="{{ route('place.show', $place->slug) }}" class="text-xs font-bold text-route-cyan-ink hover:underline">Lihat →</a>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-warn-50 px-3 py-1 text-[11px] font-bold text-warn-700 ring-1 ring-inset ring-warn-600/25">
                                        ⏳ Menunggu Verifikasi Admin
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ Preferences Modal ═══ --}}
    @if ($user->isDisabilityAccount())
    <template x-teleport="body">
        <div x-show="showPrefsModal" x-cloak
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
             @keydown.escape.window="showPrefsModal = false">
            <div x-show="showPrefsModal" x-transition.opacity class="absolute inset-0 bg-ink-900/50 backdrop-blur-sm" @click="showPrefsModal = false"></div>
            <div x-show="showPrefsModal" x-transition
                 class="relative w-full max-w-lg rounded-2xl border border-ink-900/10 bg-white p-6 shadow-2xl">
                <button @click="showPrefsModal = false" class="absolute right-4 top-4 grid h-8 w-8 place-items-center rounded-lg text-ink-400 transition hover:bg-ink-100 hover:text-ink-900" aria-label="Tutup">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
                </button>

                <h3 class="font-display text-base font-extrabold text-ink-900">⚙️ Sesuaikan Kebutuhan Saya</h3>
                <p class="mt-1 text-xs font-semibold text-ink-500">Pilih ragam disabilitas agar dashboard dan peta disesuaikan untuk kebutuhanmu.</p>

                <form method="POST" action="{{ route('user.preferences.update') }}" class="mt-5 space-y-3">
                    @csrf
                    <input type="hidden" name="account_type" value="disability">

                    @foreach (\App\Models\User::DISABILITY_TYPES as $typeKey => $typeInfo)
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-3.5 transition hover:bg-paper-dim {{ $user->disability_type === $typeKey ? 'border-route-cyan bg-route-cyan/5' : 'border-ink-900/10' }}">
                            <input type="radio" name="disability_type" value="{{ $typeKey }}"
                                   {{ $user->disability_type === $typeKey ? 'checked' : '' }}
                                   class="mt-0.5 h-4 w-4 text-route-cyan focus:ring-route-cyan">
                            <div>
                                <p class="text-xs font-bold text-ink-900">{{ $typeInfo['badge'] }}</p>
                                <p class="mt-0.5 text-[11px] text-ink-500">{{ $typeInfo['description'] }}</p>
                            </div>
                        </label>
                    @endforeach

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showPrefsModal = false"
                                class="h-10 rounded-xl border border-ink-900/10 px-4 text-xs font-bold text-ink-600 transition hover:bg-ink-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="h-10 rounded-xl bg-route-cyan px-5 text-xs font-extrabold text-white shadow-md shadow-route-cyan/20 transition hover:bg-route-cyan-ink">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardState', () => ({
            activeTab: 'reports',
            showPrefsModal: false,
        }));
    });
</script>
@endpush
