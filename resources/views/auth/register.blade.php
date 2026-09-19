@extends('layouts.app')

@section('title', 'Daftar — AksesKita')

@section('content')
<section class="relative isolate flex min-h-[calc(100vh-5rem)] items-center overflow-hidden bg-white py-16 sm:py-24">
    {{-- Subtle transit line SVG background --}}
    <div class="pointer-events-none absolute inset-0 -z-10 opacity-30" aria-hidden="true">
        <svg class="h-full w-full" preserveAspectRatio="none" viewBox="0 0 1200 700">
            <path d="M-40 480 H1240" stroke="var(--color-route-magenta)" stroke-opacity="0.14" stroke-width="8" stroke-linecap="round"/>
            <path d="M920 -40 V760" stroke="var(--color-warn-500)" stroke-opacity="0.14" stroke-width="6" stroke-linecap="round"/>
        </svg>
    </div>

    <div class="mx-auto grid w-full max-w-6xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        {{-- Left Hero Column --}}
        <div class="hidden lg:block">
            <span class="inline-flex items-center gap-2 rounded-full border border-route-cyan/20 bg-route-cyan/10 px-3.5 py-1 text-xs font-extrabold text-route-cyan-ink">
                <x-pictogram name="wheelchair" class="h-4 w-4" /> AksesKita ID
            </span>

            <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight tracking-tight text-slate-900">
                Bergabung dengan <span class="text-route-cyan">AksesKita</span>
            </h1>
            <p class="mt-4 max-w-md text-base leading-relaxed text-slate-600">
                Buat akun untuk melihat detail aksesibilitas, menyimpan tempat favorit,
                dan mendapatkan informasi terkini tentang kondisi fasilitas publik di sekitarmu.
            </p>

            <div class="mt-8 space-y-3.5 max-w-md">
                @foreach ([
                    ['locate', 'Akses peta aksesibilitas lengkap', 'Lihat skor & detail fasilitas di seluruh kota.'],
                    ['check', 'Data terverifikasi manusia', 'Setiap laporan ditinjau admin sebelum tayang.'],
                    ['community', 'Gratis & terbuka untuk semua', 'Penyandang disabilitas, keluarga, dan siapa pun.'],
                ] as $item)
                    <div class="flex items-center gap-3.5 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-route-cyan/10 text-route-cyan-ink">
                            <x-pictogram :name="$item[0]" class="h-5 w-5" :stroke="2.2" />
                        </span>
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $item[1] }}</p>
                            <p class="text-xs text-slate-500">{{ $item[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right Form Card --}}
        <div class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-xl shadow-slate-900/5 sm:p-9">
            <a href="{{ route('home') }}" class="inline-flex min-h-[44px] items-center gap-2 text-sm font-bold text-slate-500 transition hover:text-route-cyan-ink">
                <x-pictogram name="arrow" class="h-4 w-4 rotate-180" /> Kembali ke Beranda
            </a>

            <h2 class="mt-3 font-display text-2xl font-extrabold tracking-tight text-slate-900">Buat akun baru</h2>
            <p class="mt-1 text-sm text-slate-500">Gratis, tanpa biaya langganan, terbuka untuk semua warga.</p>

            @if ($errors->any())
                <div role="alert" class="mt-6 rounded-2xl border border-stop-200 bg-stop-50 p-4">
                    <p class="flex items-center gap-2 text-sm font-extrabold text-stop-700">
                        <x-pictogram name="warning" class="h-4 w-4" :stroke="2.4" /> Periksa kembali isianmu
                    </p>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-stop-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4"
                  x-data="{ accountType: '{{ old('account_type', 'disability') }}', disabilityType: '{{ old('disability_type', 'wheelchair') }}' }">
                @csrf

                {{-- Account Type Selection --}}
                <div>
                    <label class="block text-sm font-bold text-slate-800">Pilihan Tipe Akun</label>
                    <p class="text-xs text-slate-500 mb-2">Pilih profil agar AksesKita dapat mempersonalisasi filter dan rekomendasi tempat untukmu.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border-2 p-3.5 transition"
                               :class="accountType === 'disability' ? 'border-route-cyan bg-route-cyan/5 shadow-xs' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-100/60'">
                            <input type="radio" name="account_type" value="disability" x-model="accountType" class="mt-1 text-route-cyan focus:ring-route-cyan">
                            <div>
                                <div class="flex items-center gap-1.5 font-black text-slate-900 text-sm">
                                    <span>♿</span>
                                    <span>Penyandang Disabilitas</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 font-medium leading-normal">Disesuaikan dengan kebutuhan mobilitas, netra, rungu, atau sensorik.</p>
                            </div>
                        </label>

                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border-2 p-3.5 transition"
                               :class="accountType === 'contributor' ? 'border-route-cyan bg-route-cyan/5 shadow-xs' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-100/60'">
                            <input type="radio" name="account_type" value="contributor" x-model="accountType" class="mt-1 text-route-cyan focus:ring-route-cyan">
                            <div>
                                <div class="flex items-center gap-1.5 font-black text-slate-900 text-sm">
                                    <span>🤝</span>
                                    <span>Relawan / Pelapor Umum</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 font-medium leading-normal">Masyarakat umum yang ingin melapor dan memperbarui fasilitas ramah.</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Disability Specific Selection (shown when accountType === 'disability') --}}
                <div x-show="accountType === 'disability'" x-cloak x-transition class="rounded-2xl border border-route-cyan/30 bg-route-cyan/[0.04] p-3.5 space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-route-cyan-ink">
                        Pilih Ragam Kebutuhan Aksesibilitas
                    </label>
                    <p class="text-xs text-slate-600">Filter dan informasi peta akan otomatis disesuaikan dengan kebutuhan pilihanmu:</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                        @foreach (\App\Models\User::DISABILITY_TYPES as $key => $dis)
                            <label class="flex cursor-pointer items-start gap-2.5 rounded-xl border p-2.5 bg-white transition"
                                   :class="disabilityType === '{{ $key }}' ? 'border-route-cyan ring-2 ring-route-cyan/20 shadow-2xs font-extrabold' : 'border-slate-200 hover:border-slate-300 font-semibold'">
                                <input type="radio" name="disability_type" value="{{ $key }}" x-model="disabilityType" class="mt-0.5 text-route-cyan focus:ring-route-cyan">
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-900 font-bold leading-tight">{{ $dis['badge'] }}</p>
                                    <p class="text-[11px] text-slate-500 leading-snug mt-0.5 line-clamp-1">{{ $dis['description'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-bold text-slate-800">Nama lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                           class="mt-1.5 h-12 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                           placeholder="Nama kamu">
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-800">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                           class="mt-1.5 h-12 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                           placeholder="nama@email.com">
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-bold text-slate-800">Password</label>
                    <div class="relative mt-1.5">
                        <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                               class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 pr-12 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                               placeholder="Minimal 8 karakter">
                        <button type="button" @click="show = !show" :aria-pressed="show" aria-label="Tampilkan atau sembunyikan password"
                                class="absolute inset-y-0 right-0 grid w-12 place-items-center text-slate-400 hover:text-slate-700">
                            <x-pictogram name="vision" class="h-5 w-5" x-show="!show" />
                            <x-pictogram name="blocked" class="h-5 w-5" x-show="show" x-cloak />
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-slate-800">Konfirmasi password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="mt-1.5 h-12 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                           placeholder="Ulangi password">
                </div>

                <div class="pt-2">
                    <button type="submit" class="flex h-14 w-full items-center justify-center gap-2.5 rounded-2xl bg-route-cyan text-base font-extrabold text-white shadow-lg shadow-route-cyan/25 transition hover:bg-route-cyan-ink focus-visible:outline-route-cyan">
                        <x-pictogram name="arrow" class="h-5 w-5" /> Daftar Sekarang
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-sm font-medium text-slate-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-extrabold text-route-cyan-ink hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</section>
@endsection
