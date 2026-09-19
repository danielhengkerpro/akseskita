@extends('layouts.dashboard')

@section('title', 'Ajukan Tempat Baru — AksesKita')
@section('heading', 'Ajukan Tempat Baru')
@section('subheading', 'Daftarkan tempat atau fasilitas publik di kotamu. Pengajuan akan ditinjau dan diverifikasi oleh admin.')

@section('content')
<div class="mx-auto max-w-4xl" x-data="createPlaceForm()">
    <form method="POST" action="{{ route('user.places.store') }}" class="space-y-6">
        @csrf

        {{-- Notice Banner --}}
        <div class="flex items-start gap-3.5 rounded-2xl border border-route-cyan/20 bg-route-cyan/5 p-4 sm:p-5">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-route-cyan text-white text-base font-bold">
                ℹ️
            </span>
            <div>
                <p class="font-bold text-slate-900 text-sm">Alur Verifikasi Tempat Baru</p>
                <p class="mt-0.5 text-xs text-slate-600 leading-relaxed">
                    Setiap tempat baru yang diajukan oleh masyarakat atau penyandang disabilitas akan melalui proses verifikasi oleh tim admin AksesKita untuk memastikan akurasi data sebelum diterbitkan secara resmi di peta publik.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div role="alert" class="rounded-2xl border border-stop-200 bg-stop-50 p-4">
                <p class="flex items-center gap-2 text-sm font-extrabold text-stop-700">
                    <x-pictogram name="warning" class="h-4 w-4" :stroke="2.4" /> Mohon periksa kembali isian formulir:
                </p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-xs text-stop-700 font-semibold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Step 1: Informasi Utama Tempat --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="font-display font-black text-slate-900 text-base flex items-center gap-2">
                    <span class="grid h-6 w-6 place-items-center rounded-full bg-slate-900 text-xs text-white">1</span>
                    Informasi Tempat
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Nama dan jenis tempat yang ingin kamu daftarkan.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-black uppercase tracking-wider text-slate-700">Nama Tempat <span class="text-stop-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                           class="mt-1.5 h-12 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                           placeholder="Misal: Halte CSW Transjakarta / Mall Kota Kasablanka">
                </div>

                <div>
                    <label for="category" class="block text-xs font-black uppercase tracking-wider text-slate-700">Kategori Tempat <span class="text-stop-500">*</span></label>
                    <select id="category" name="category" required
                            class="mt-1.5 h-12 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 text-sm font-semibold text-slate-900 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition">
                        <option value="">— Pilih Kategori —</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="address" class="block text-xs font-black uppercase tracking-wider text-slate-700">Alamat Lengkap <span class="text-stop-500">*</span></label>
                    <input id="address" name="address" type="text" value="{{ old('address') }}" required
                           class="mt-1.5 h-12 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                           placeholder="Jalan, kelurahan, atau patokan lokasi">
                </div>
            </div>
        </div>

        {{-- Step 2: Titik Koordinat Peta --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="font-display font-black text-slate-900 text-base flex items-center gap-2">
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-slate-900 text-xs text-white">2</span>
                        Pilih Titik Lokasi pada Peta
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Klik pada peta di bawah ini untuk menandai posisi lokasi dengan tepat.</p>
                </div>
                <button type="button" @click="locateMe()"
                        class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-route-cyan/30 bg-route-cyan/10 px-3 text-xs font-bold text-route-cyan-ink transition hover:bg-route-cyan hover:text-white">
                    <x-pictogram name="locate" class="h-3.5 w-3.5" />
                    <span>Gunakan GPS Saya</span>
                </button>
            </div>

            <div id="pin-map" class="h-64 w-full rounded-xl border border-slate-200 z-0"></div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="latitude" class="block text-[11px] font-bold text-slate-600">Latitude <span class="text-stop-500">*</span></label>
                    <input id="latitude" name="latitude" type="text" x-model="lat" value="{{ old('latitude', '-6.2088') }}" required readonly
                           class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 text-xs font-mono font-bold text-slate-700">
                </div>
                <div>
                    <label for="longitude" class="block text-[11px] font-bold text-slate-600">Longitude <span class="text-stop-500">*</span></label>
                    <input id="longitude" name="longitude" type="text" x-model="lng" value="{{ old('longitude', '106.8456') }}" required readonly
                           class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 text-xs font-mono font-bold text-slate-700">
                </div>
            </div>
        </div>

        {{-- Step 3: Checklist Fasilitas Aksesibilitas --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="font-display font-black text-slate-900 text-base flex items-center gap-2">
                    <span class="grid h-6 w-6 place-items-center rounded-full bg-slate-900 text-xs text-white">3</span>
                    Checklist 6 Fasilitas Aksesibilitas
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Centang fasilitas disabilitas yang terbukti tersedia dan berfungsi di lokasi ini.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3.5 transition hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="has_ramp" value="1" @checked(old('has_ramp'))
                           class="mt-0.5 h-4 w-4 rounded text-route-cyan focus:ring-route-cyan">
                    <div>
                        <p class="text-xs font-bold text-slate-900">♿ Ramp / Bidang Miring</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Tersedia jalur landai pengganti tangga untuk kursi roda / stroller.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3.5 transition hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="has_lift" value="1" @checked(old('has_lift'))
                           class="mt-0.5 h-4 w-4 rounded text-route-cyan focus:ring-route-cyan">
                    <div>
                        <p class="text-xs font-bold text-slate-900">🛗 Lift Ramah Disabilitas</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Lift beroperasi dengan pintu lebar dan posisi tombol yang mudah dijangkau.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3.5 transition hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="has_accessible_toilet" value="1" @checked(old('has_accessible_toilet'))
                           class="mt-0.5 h-4 w-4 rounded text-route-cyan focus:ring-route-cyan">
                    <div>
                        <p class="text-xs font-bold text-slate-900">🚻 Toilet Khusus Disabilitas</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Toilet luas dengan pegangan tangan (grab bar) dan pintu dorong/geser.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3.5 transition hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="has_guiding_block" value="1" @checked(old('has_guiding_block'))
                           class="mt-0.5 h-4 w-4 rounded text-route-cyan focus:ring-route-cyan">
                    <div>
                        <p class="text-xs font-bold text-slate-900">🦯 Ubin Pemandu (Guiding Block)</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Ubin timbul kuning di lantai sebagai pemandu arah bagi penyandang netra.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3.5 transition hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="wide_door" value="1" @checked(old('wide_door'))
                           class="mt-0.5 h-4 w-4 rounded text-route-cyan focus:ring-route-cyan">
                    <div>
                        <p class="text-xs font-bold text-slate-900">🚪 Pintu Masuk Lebar (&gt;90cm)</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pintu utama cukup luas untuk dilewati pengguna kursi roda mandiri.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3.5 transition hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="braille_lift" value="1" @checked(old('braille_lift'))
                           class="mt-0.5 h-4 w-4 rounded text-route-cyan focus:ring-route-cyan">
                    <div>
                        <p class="text-xs font-bold text-slate-900">⠃ Tombol / Panel Braille</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Tombol lift atau papan petunjuk dilengkapi huruf Braille / audio.</p>
                    </div>
                </label>
            </div>

            <div class="pt-2">
                <label for="notes" class="block text-xs font-black uppercase tracking-wider text-slate-700">Catatan Khusus Aksesibilitas (Opsional)</label>
                <textarea id="notes" name="notes" rows="3"
                          class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-xs font-semibold text-slate-900 placeholder:text-slate-400 focus:border-route-cyan focus:bg-white focus:ring-4 focus:ring-route-cyan/10 focus:outline-none transition"
                          placeholder="Misal: Ramp hanya tersedia di sisi barat halte, toilet ramah disabilitas berada di dekat loket utama.">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Submit buttons --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('user.dashboard') }}"
               class="flex h-12 items-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                Batal
            </a>
            <button type="submit"
                    class="flex h-12 items-center gap-2 rounded-xl bg-route-cyan px-6 text-sm font-black text-white shadow-md shadow-route-cyan/25 transition hover:bg-route-cyan-ink focus-visible:outline-route-cyan">
                <x-pictogram name="check" class="h-4 w-4" />
                <span>Kirim Pengajuan Tempat</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('createPlaceForm', () => ({
            map: null,
            marker: null,
            lat: '{{ old('latitude', '-6.2088') }}',
            lng: '{{ old('longitude', '106.8456') }}',

            init() {
                this.$nextTick(() => this.initMap());
            },

            initMap() {
                const el = document.getElementById('pin-map');
                if (!el || typeof window.L === 'undefined') return;

                const lat = parseFloat(this.lat) || -6.2088;
                const lng = parseFloat(this.lng) || 106.8456;

                this.map = L.map(el, { center: [lat, lng], zoom: 14 });
                window.AksesKitaMap.baseTiles().addTo(this.map);

                this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.lat = pos.lat.toFixed(8);
                    this.lng = pos.lng.toFixed(8);
                });

                this.map.on('click', (e) => {
                    this.lat = e.latlng.lat.toFixed(8);
                    this.lng = e.latlng.lng.toFixed(8);
                    this.marker.setLatLng(e.latlng);
                });
            },

            locateMe() {
                if (!navigator.geolocation) {
                    alert('Geolocation tidak didukung pada browser ini.');
                    return;
                }
                navigator.geolocation.getCurrentPosition((pos) => {
                    this.lat = pos.coords.latitude.toFixed(8);
                    this.lng = pos.coords.longitude.toFixed(8);
                    if (this.map && this.marker) {
                        this.map.setView([pos.coords.latitude, pos.coords.longitude], 16);
                        this.marker.setLatLng([pos.coords.latitude, pos.coords.longitude]);
                    }
                }, (err) => {
                    alert('Gagal mengambil lokasi GPS: ' + err.message);
                });
            }
        }));
    });
</script>
@endpush
