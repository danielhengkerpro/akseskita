<footer class="relative overflow-hidden border-t border-slate-200/80 bg-slate-50 text-slate-600">
    {{-- Route-line motif across the top: five coloured lines with stop nodes --}}
    <div class="relative h-2 w-full overflow-hidden" aria-hidden="true">
        <div class="absolute inset-x-0 top-0 flex h-2">
            <span class="h-full flex-1" style="background: var(--color-go-500)"></span>
            <span class="h-full flex-1" style="background: var(--color-route-cyan-soft)"></span>
            <span class="h-full flex-1" style="background: var(--color-route-violet-soft)"></span>
            <span class="h-full flex-1" style="background: var(--color-warn-500)"></span>
            <span class="h-full flex-1" style="background: var(--color-stop-500)"></span>
        </div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1.5fr_1fr_1fr]">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-route-cyan text-white shadow-md shadow-route-cyan/20">
                        <x-pictogram name="wheelchair" class="h-6 w-6" :stroke="2.2" />
                    </span>
                    <span class="text-xl font-extrabold tracking-tight text-slate-900">Akses<span class="text-route-cyan">Kita</span></span>
                </div>
                <p class="mt-5 max-w-md text-sm leading-relaxed text-slate-600">
                    Peta aksesibilitas fasilitas publik di Jakarta &amp; Tangerang — dibuat warga, untuk warga.
                    Membantu penyandang disabilitas, lansia, dan keluarga mereka bergerak dengan percaya diri.
                </p>

                {{-- Status legend drawn as real route lines with nodes --}}
                <ul class="mt-7 space-y-2.5" aria-label="Tingkat aksesibilitas">
                    <li class="flex items-center gap-3">
                        <span class="relative flex h-3 w-16 items-center text-go-500"><span class="route-line w-full"></span><span class="route-node left-0 !h-3.5 !w-3.5 !border-2 !border-go-500 !bg-white"></span></span>
                        <span class="text-xs font-bold text-slate-700"><span class="text-go-700">&#9670; Ramah</span> — semua fasilitas tersedia</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="relative flex h-3 w-16 items-center text-warn-500"><span class="route-line w-full"></span><span class="route-node left-0 !h-3.5 !w-3.5 !border-2 !border-warn-500 !bg-white"></span></span>
                        <span class="text-xs font-bold text-slate-700"><span class="text-warn-700">&#9650; Terbatas</span> — sebagian fasilitas ada</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="relative flex h-3 w-16 items-center text-stop-500"><span class="route-line w-full"></span><span class="route-node left-0 !h-3.5 !w-3.5 !border-2 !border-stop-500 !bg-white"></span></span>
                        <span class="text-xs font-bold text-slate-700"><span class="text-stop-700">&#9679; Berbahaya</span> — hindari bila memungkinkan</span>
                    </li>
                </ul>
            </div>

            <nav aria-label="Jelajahi">
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Jelajahi</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('home') }}" class="inline-flex min-h-[28px] items-center py-1 font-medium text-slate-600 transition hover:text-route-cyan-ink hover:underline">Beranda</a></li>
                    <li><a href="{{ route('map.index') }}" class="inline-flex min-h-[28px] items-center py-1 font-medium text-slate-600 transition hover:text-route-cyan-ink hover:underline">Peta Aksesibilitas</a></li>
                    <li><a href="{{ route('home') }}#cara-kerja" class="inline-flex min-h-[28px] items-center py-1 font-medium text-slate-600 transition hover:text-route-cyan-ink hover:underline">Cara Berkontribusi</a></li>
                    <li><a href="{{ route('home') }}#statistik" class="inline-flex min-h-[28px] items-center py-1 font-medium text-slate-600 transition hover:text-route-cyan-ink hover:underline">Statistik Data</a></li>
                </ul>
            </nav>

            <nav aria-label="Untuk kontributor">
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Untuk Kontributor</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('login') }}" class="inline-flex min-h-[28px] items-center py-1 font-medium text-slate-600 transition hover:text-route-cyan-ink hover:underline">Masuk</a></li>
                    <li><a href="{{ route('register') }}" class="inline-flex min-h-[28px] items-center py-1 font-medium text-slate-600 transition hover:text-route-cyan-ink hover:underline">Daftar Akun</a></li>
                    <li><a href="{{ route('user.report.create') }}" class="inline-flex min-h-[28px] items-center py-1 font-medium text-slate-600 transition hover:text-route-cyan-ink hover:underline">Laporkan Fasilitas</a></li>
                    <li><a href="{{ route('admin.login') }}" class="inline-flex min-h-[28px] items-center py-1 font-semibold text-slate-700 transition hover:text-route-cyan-ink hover:underline">Portal Verifikator (Admin)</a></li>
                    <li><a href="{{ route('api.places') }}" class="inline-flex min-h-[28px] items-center py-1 font-medium text-slate-600 transition hover:text-route-cyan-ink hover:underline">API Data (JSON)</a></li>
                </ul>
            </nav>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-slate-200/80 pt-8 text-xs text-slate-500 sm:flex-row">
            <p>&copy; {{ date('Y') }} AksesKita. Dibuat untuk kota yang inklusif.</p>
            <p>Dirancang sesuai WCAG 2.2 AA &middot; Peta oleh <a href="https://www.openstreetmap.org/" class="underline hover:text-route-cyan-ink">OpenStreetMap</a> &amp; Leaflet.js</p>
        </div>
    </div>
</footer>
