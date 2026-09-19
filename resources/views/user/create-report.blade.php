@extends('layouts.dashboard')

@section('title', 'Buat Laporan — AksesKita')
@section('heading', 'Buat Laporan Fasilitas')
@section('subheading', 'Bantu perbarui data dengan laporan kondisi fasilitas yang kamu temui.')

@section('content')
<div x-data="reportForm()" class="grid gap-8 lg:grid-cols-[1.4fr_1fr]">
    {{-- Form --}}
    <form method="POST" action="{{ route('user.report.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="rounded-2xl border border-stop-500/40 bg-stop-50 p-4">
                <ul class="list-inside list-disc space-y-1 text-sm font-semibold text-stop-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-bold text-ink-900">Informasi Tempat</h2>
            <p class="text-sm text-ink-900/50">Pilih tempat terdaftar atau tulis nama tempat baru.</p>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="place_id" class="block text-sm font-semibold text-ink-900">Tempat terdaftar (opsional)</label>
                    <select id="place_id" name="place_id" x-model="placeId" @change="syncFromPlace()"
                            class="mt-2 w-full rounded-2xl border-0 bg-paper-dim py-3 pl-4 pr-10 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan">
                        <option value="">— Tempat baru / tidak dalam daftar —</option>
                        @foreach ($places as $place)
                            <option value="{{ $place->id }}"
                                    data-name="{{ $place->name }}"
                                    data-lat="{{ $place->latitude }}"
                                    data-lng="{{ $place->longitude }}"
                                    @selected(old('place_id') == $place->id)>
                                {{ $place->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="place_name" class="block text-sm font-semibold text-ink-900">Nama tempat <span class="text-stop-500">*</span></label>
                    <input id="place_name" name="place_name" type="text" x-model="placeName" value="{{ old('place_name') }}" required
                           class="mt-2 w-full rounded-2xl border-0 bg-paper-dim px-4 py-3 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 placeholder:text-ink-900/40 focus:ring-2 focus:ring-route-cyan"
                           placeholder="Misal: Stasiun MRT Bundaran HI">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="latitude" class="block text-sm font-semibold text-ink-900">Latitude</label>
                        <input id="latitude" name="latitude" type="text" x-model="lat" value="{{ old('latitude') }}"
                               class="mt-2 w-full rounded-2xl border-0 bg-paper-dim px-4 py-3 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan"
                               placeholder="-6.19375000">
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-semibold text-ink-900">Longitude</label>
                        <input id="longitude" name="longitude" type="text" x-model="lng" value="{{ old('longitude') }}"
                               class="mt-2 w-full rounded-2xl border-0 bg-paper-dim px-4 py-3 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan"
                               placeholder="106.82293000">
                    </div>
                </div>

                <button type="button" @click="locate()" class="inline-flex h-11 items-center gap-2 rounded-xl border border-ink-900/10 px-4 text-sm font-bold text-ink-700 transition hover:border-route-cyan hover:text-route-cyan-ink">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-3a5 5 0 100-10 5 5 0 000 10z" clip-rule="evenodd"/></svg>
                    Gunakan lokasiku saat ini
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <h2 class="font-bold text-ink-900">Detail Laporan</h2>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="description" class="block text-sm font-semibold text-ink-900">Deskripsi kondisi <span class="text-stop-500">*</span></label>
                    <textarea id="description" name="description" rows="5" required minlength="15"
                              class="mt-2 w-full rounded-2xl border-0 bg-paper-dim px-4 py-3 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 placeholder:text-ink-900/40 focus:ring-2 focus:ring-route-cyan"
                              placeholder="Jelaskan kondisi fasilitas: apakah ada ramp, lift berfungsi, ubin pemandu, toilet disabilitas, dsb.">{{ old('description') }}</textarea>
                </div>

                <div x-data="{ preview: null, name: null }">
                    <label for="photo" class="block text-sm font-semibold text-ink-900">Foto bukti (opsional)</label>
                    <div class="mt-2 flex items-center gap-4">
                        <label class="flex flex-1 cursor-pointer items-center gap-3 rounded-2xl border-2 border-dashed border-ink-900/10 bg-paper-dim px-4 py-4 transition hover:border-route-cyan">
                            <svg class="h-6 w-6 text-ink-900/40" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 5.25A2.25 2.25 0 013.25 3h13.5A2.25 2.25 0 0119 5.25v9.5A2.25 2.25 0 0116.75 17H3.25A2.25 2.25 0 011 14.75v-9.5zm15.5 9.5a.75.75 0 01-.75.75H4.25a.75.75 0 01-.75-.75v-9.5a.75.75 0 01.75-.75h13.5a.75.75 0 01.75.75v9.5zM10 7a3 3 0 100 6 3 3 0 000-6z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-ink-900/60" x-text="name || 'Pilih foto (JPG/PNG, maks 4MB)'"></span>
                            <input id="photo" name="photo" type="file" accept="image/*" class="hidden"
                                   x-ref="input"
                                   @change="const f = $event.target.files[0]; if (f) { name = f.name; preview = URL.createObjectURL(f); }">
                        </label>
                        <template x-if="preview">
                            <img :src="preview" alt="Pratinjau" class="h-20 w-20 rounded-xl object-cover ring-1 ring-ink-900/10">
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex h-13 items-center gap-2 rounded-2xl bg-go-700 px-7 py-3.5 text-sm font-extrabold text-white transition hover:bg-go-600">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M3.105 2.288a.75.75 0 00-.826.95l1.414 4.926A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.897 28.897 0 0015.293-7.155.75.75 0 000-1.114A28.897 28.897 0 003.105 2.288z"/></svg>
                Kirim Laporan
            </button>
            <a href="{{ route('user.dashboard') }}" class="inline-flex h-13 items-center rounded-2xl px-5 text-sm font-bold text-ink-500 hover:text-ink-900">Batal</a>
        </div>
    </form>

    {{-- Peta pratinjau lokasi --}}
    <div class="lg:sticky lg:top-28 lg:self-start">
        <div class="overflow-hidden rounded-2xl border border-ink-900/5 bg-white shadow-sm">
            <div class="border-b border-ink-900/5 px-5 py-3">
                <p class="text-sm font-bold text-ink-900">Pratinjau Lokasi</p>
                <p class="text-xs text-ink-900/50">Klik peta untuk menandai titik laporan.</p>
            </div>
            <div id="report-map" class="h-[420px] w-full"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reportForm', () => ({
            placeId: @js((string) old('place_id')),
            placeName: @js((string) old('place_name')),
            lat: @js((string) old('latitude')),
            lng: @js((string) old('longitude')),
            map: null,
            marker: null,

            init() {
                this.$nextTick(() => this.initMap());
            },

            initMap() {
                const el = document.getElementById('report-map');
                if (!el || typeof window.L === 'undefined') return;

                const start = [parseFloat(this.lat) || -6.21, parseFloat(this.lng) || 106.78];
                this.map = L.map(el, { center: start, zoom: 12 });
                window.AksesKitaMap.baseTiles().addTo(this.map);

                if (this.lat && this.lng) {
                    this.setMarker(start[0], start[1]);
                }

                this.map.on('click', (e) => {
                    this.lat = e.latlng.lat.toFixed(8);
                    this.lng = e.latlng.lng.toFixed(8);
                    this.setMarker(e.latlng.lat, e.latlng.lng);
                });
            },

            setMarker(lat, lng) {
                if (this.marker) {
                    this.marker.setLatLng([lat, lng]);
                } else {
                    this.marker = L.marker([lat, lng]).addTo(this.map);
                }
            },

            syncFromPlace() {
                const select = document.getElementById('place_id');
                const option = select.options[select.selectedIndex];
                if (!option || !option.value) return;

                this.placeName = option.dataset.name || '';
                this.lat = option.dataset.lat || '';
                this.lng = option.dataset.lng || '';

                const lat = parseFloat(this.lat);
                const lng = parseFloat(this.lng);
                if (this.map && !isNaN(lat) && !isNaN(lng)) {
                    this.map.setView([lat, lng], 16);
                    this.setMarker(lat, lng);
                }
            },

            locate() {
                if (!navigator.geolocation) return;
                navigator.geolocation.getCurrentPosition((pos) => {
                    this.lat = pos.coords.latitude.toFixed(8);
                    this.lng = pos.coords.longitude.toFixed(8);
                    if (this.map) {
                        this.map.setView([pos.coords.latitude, pos.coords.longitude], 16);
                        this.setMarker(pos.coords.latitude, pos.coords.longitude);
                    }
                });
            },
        }));
    });
</script>
@endpush
