@extends('layouts.app')

@section('title', 'Masuk — AksesKita')

@section('content')
<section class="relative isolate flex min-h-[calc(100vh-5rem)] items-center overflow-hidden bg-white py-16 sm:py-24">
    {{-- Subtle transit line SVG background --}}
    <div class="pointer-events-none absolute inset-0 -z-10 opacity-30" aria-hidden="true">
        <svg class="h-full w-full" preserveAspectRatio="none" viewBox="0 0 1200 700">
            <path d="M-40 200 H1240" stroke="var(--color-route-cyan)" stroke-opacity="0.16" stroke-width="8" stroke-linecap="round"/>
            <path d="M220 -40 V760" stroke="var(--color-route-violet)" stroke-opacity="0.12" stroke-width="6" stroke-linecap="round"/>
        </svg>
    </div>

    <div class="mx-auto grid w-full max-w-6xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        {{-- Left Hero Column --}}
        <div class="hidden lg:block">
            <span class="inline-flex items-center gap-2 rounded-full border border-route-cyan/20 bg-route-cyan/10 px-3.5 py-1 text-xs font-extrabold text-route-cyan-ink">
                <x-pictogram name="wheelchair" class="h-4 w-4" /> AksesKita ID
            </span>

            <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight tracking-tight text-slate-900">
                Selamat datang kembali di <span class="text-route-cyan">AksesKita</span>
            </h1>
            <p class="mt-4 max-w-md text-base leading-relaxed text-slate-600">
                Masuk untuk melanjutkan kontribusimu: melaporkan kondisi fasilitas, memantau status verifikasi,
                dan membantu menciptakan kota yang lebih inklusif.
            </p>

            <div class="mt-8 grid max-w-md gap-3.5">
                @foreach ([
                    ['check', 'Peta status ramah, terbatas, & berbahaya', 'Cek kondisi sebelum bepergian'],
                    ['guiding-block', 'Detail ubin pemandu & tombol Braille', 'Aksesibilitas sensorik lengkap'],
                    ['wheelchair', 'Info ramp, lift & toilet disabilitas', 'Dimensi fisik ramah mobilitas'],
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

            <h2 class="mt-3 font-display text-2xl font-extrabold tracking-tight text-slate-900">Masuk ke akunmu</h2>
            <p class="mt-1 text-sm text-slate-500">Gunakan email dan password yang telah kamu daftarkan.</p>

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

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-800">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="mt-2 h-12 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                           placeholder="nama@email.com">
                </div>

                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-bold text-slate-800">Password</label>
                    </div>
                    <div class="relative mt-2">
                        <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password"
                               class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 pr-12 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                               placeholder="Minimal 8 karakter">
                        <button type="button" @click="show = !show" :aria-pressed="show" aria-label="Tampilkan atau sembunyikan password"
                                class="absolute inset-y-0 right-0 grid w-12 place-items-center text-slate-400 hover:text-slate-700">
                            <x-pictogram name="vision" class="h-5 w-5" x-show="!show" />
                            <x-pictogram name="blocked" class="h-5 w-5" x-show="show" x-cloak />
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex min-h-[44px] cursor-pointer items-center gap-2.5 text-sm font-medium text-slate-700">
                        <input type="checkbox" name="remember" class="h-5 w-5 rounded border-slate-300 text-route-cyan focus:ring-route-cyan">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <button type="submit" class="flex h-14 w-full items-center justify-center gap-2.5 rounded-2xl bg-route-cyan text-base font-extrabold text-white shadow-lg shadow-route-cyan/25 transition hover:bg-route-cyan-ink focus-visible:outline-route-cyan">
                    <x-pictogram name="arrow" class="h-5 w-5" /> Masuk ke Akun
                </button>
            </form>

            <div class="mt-6 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-3.5 text-center text-xs text-slate-600">
                <span>Pengelola atau Verifikator?</span>
                <a href="{{ route('admin.login') }}" class="ml-1 font-extrabold text-route-cyan-ink hover:underline">
                    Masuk Portal Admin &rarr;
                </a>
            </div>

            <p class="mt-6 text-center text-sm font-medium text-slate-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-extrabold text-route-cyan-ink hover:underline">Daftar sekarang</a>
            </p>
        </div>
    </div>
</section>
@endsection
