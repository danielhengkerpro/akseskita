@extends('layouts.app')

@section('title', 'Portal Admin & Verifikator — AksesKita')

@section('content')
<section class="relative isolate flex min-h-[calc(100vh-5rem)] items-center overflow-hidden bg-white py-16 sm:py-24">
    {{-- Subtle transit line SVG background --}}
    <div class="pointer-events-none absolute inset-0 -z-10 opacity-30" aria-hidden="true">
        <svg class="h-full w-full" preserveAspectRatio="none" viewBox="0 0 1200 700">
            <path d="M-40 350 H1240" stroke="var(--color-route-cyan)" stroke-opacity="0.14" stroke-width="8" stroke-linecap="round"/>
            <path d="M700 -40 V760" stroke="var(--color-warn-400)" stroke-opacity="0.10" stroke-width="6" stroke-linecap="round"/>
            <path d="M-40 550 H1240" stroke="var(--color-route-violet)" stroke-opacity="0.08" stroke-width="5" stroke-linecap="round"/>
        </svg>
    </div>

    <div class="mx-auto grid w-full max-w-6xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        {{-- Left Hero Column --}}
        <div class="hidden lg:block">
            <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3.5 py-1 text-xs font-extrabold text-amber-800">
                <x-pictogram name="verification" class="h-4 w-4" /> Portal Admin & Verifikator
            </span>

            <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight tracking-tight text-slate-900">
                Panel Kontrol <span class="text-route-cyan">AksesKita</span>
            </h1>
            <p class="mt-4 max-w-md text-base leading-relaxed text-slate-600">
                Akses khusus untuk verifikasi fasilitas, mengelola data laporan warga,
                dan memastikan keakuratan peta aksesibilitas kota.
            </p>

            <div class="mt-8 grid max-w-md gap-3.5">
                @foreach ([
                    ['check', 'Verifikasi laporan aksesibilitas', 'Konfirmasi & validasi data warga'],
                    ['map-pin', 'Kelola data fasilitas publik', 'Tambah, edit & moderasi tempat'],
                    ['community', 'Pantau aktivitas kontributor', 'Statistik & manajemen pengguna'],
                ] as $item)
                    <div class="flex items-center gap-3.5 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-amber-50 text-amber-700">
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
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-extrabold text-amber-800 lg:hidden">
                <x-pictogram name="verification" class="h-3.5 w-3.5" /> PORTAL ADMIN
            </div>

            <h2 class="mt-3 font-display text-2xl font-extrabold tracking-tight text-slate-900">Masuk ke Panel Admin</h2>
            <p class="mt-1 text-sm text-slate-500">Gunakan kredensial administrator untuk mengakses panel kontrol.</p>

            @if ($errors->any())
                <div role="alert" class="mt-6 rounded-2xl border border-stop-200 bg-stop-50 p-4">
                    <p class="flex items-center gap-2 text-sm font-extrabold text-stop-700">
                        <x-pictogram name="warning" class="h-4 w-4" :stroke="2.4" /> Gagal masuk portal
                    </p>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-stop-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div role="status" class="mt-6 rounded-2xl border border-go-200 bg-go-50 p-4 text-sm font-bold text-go-700 flex items-center gap-2">
                    <x-pictogram name="check" class="h-4 w-4 shrink-0" />
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="admin-email" class="block text-sm font-bold text-slate-800">Email Administrator</label>
                    <input id="admin-email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="mt-2 h-12 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                           placeholder="admin@gmail.com">
                </div>

                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between">
                        <label for="admin-password" class="block text-sm font-bold text-slate-800">Kata Sandi</label>
                    </div>
                    <div class="relative mt-2">
                        <input id="admin-password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password"
                               class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 pr-12 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                               placeholder="••••••••">
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
                        Ingat sesi di perangkat ini
                    </label>
                </div>

                <button type="submit" class="flex h-14 w-full items-center justify-center gap-2.5 rounded-2xl bg-route-cyan text-base font-extrabold text-white shadow-lg shadow-route-cyan/25 transition hover:bg-route-cyan-ink focus-visible:outline-route-cyan">
                    <x-pictogram name="check" class="h-5 w-5" /> Masuk ke Panel Admin
                </button>
            </form>

            {{-- Link to public login --}}
            <div class="mt-6 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-3.5 text-center text-xs text-slate-600">
                <span>Bukan administrator platform?</span>
                <a href="{{ route('login') }}" class="ml-1 font-extrabold text-route-cyan-ink hover:underline">
                    Login Warga / Kontributor &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
