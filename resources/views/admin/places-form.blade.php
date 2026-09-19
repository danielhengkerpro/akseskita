@extends('layouts.dashboard')

@section('title', ($place->exists ? 'Edit' : 'Tambah') . ' Tempat — AksesKita')
@section('heading', $place->exists ? 'Edit Tempat' : 'Tambah Tempat Baru')
@section('subheading', 'Skor kelayakan dihitung otomatis dari fitur aksesibilitas yang dipilih.')

@section('content')
<div x-data="placeForm()" class="grid gap-8 lg:grid-cols-[1.4fr_1fr]">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

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

            <div class="mt-5 space-y-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-ink-900">Nama tempat <span class="text-stop-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $place->name) }}" required
                           class="mt-2 w-full rounded-2xl border-0 bg-paper-dim px-4 py-3 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan">
                </div>

                <div>
                    <label for="category" class="block text-sm font-semibold text-ink-900">Kategori <span class="text-stop-500">*</span></label>
                    <select id="category" name="category" required
                            class="mt-2 w-full rounded-2xl border-0 bg-paper-dim py-3 pl-4 pr-10 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan">
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}" @selected(old('category', $place->category) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-ink-900">Alamat <span class="text-stop-500">*</span></label>
                    <textarea id="address" name="address" rows="3" required
                              class="mt-2 w-full rounded-2xl border-0 bg-paper-dim px-4 py-3 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan">{{ old('address', $place->address) }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="latitude" class="block text-sm font-semibold text-ink-900">Latitude <span class="text-stop-500">*</span></label>
                        <input id="latitude" name="latitude" type="text" x-model="lat" value="{{ old('latitude', $place->latitude) }}" required
                               class="mt-2 w-full rounded-2xl border-0 bg-paper-dim px-4 py-3 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan">
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-semibold text-ink-900">Longitude <span class="text-stop-500">*</span></label>
                        <input id="longitude" name="longitude" type="text" x-model="lng" value="{{ old('longitude', $place->longitude) }}" required
                               class="mt-2 w-full rounded-2xl border-0 bg-paper-dim px-4 py-3 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan">
                    </div>
                </div>

                <label class="flex items-center gap-2.5 text-sm font-semibold text-ink-900">
                    <input type="hidden" name="is_verified" value="0">
                    <input type="checkbox" name="is_verified" value="1" @checked(old('is_verified', $place->is_verified))
                           class="h-4 w-4 rounded border-ink-900/20 text-route-cyan focus:ring-route-cyan">
                    Tandai sebagai terverifikasi
                </label>
            </div>
        </div>

        <div class="rounded-2xl border border-ink-900/5 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-ink-900">Fitur Aksesibilitas</h2>
                    <p class="text-sm text-ink-900/50">Centang fitur yang tersedia di lokasi ini.</p>
                </div>
                <span class="rounded-full bg-route-cyan/12 px-3 py-1.5 text-sm font-extrabold text-route-cyan-ink">
                    Skor: <span x-text="score"></span>/100
                </span>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach (\App\Models\AccessibilityFeature::criteria() as $criterion)
                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-ink-900/5 bg-paper-dim p-4 transition hover:border-route-cyan">
                        <input type="hidden" name="{{ $criterion['key'] }}" value="0">
                        <input type="checkbox" name="{{ $criterion['key'] }}" value="1" x-model="features['{{ $criterion['key'] }}']"
                               @checked(old($criterion['key'], $features->{$criterion['key']}))
                               class="h-5 w-5 rounded border-ink-900/20 text-route-cyan focus:ring-route-cyan">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-route-cyan/12 text-route-cyan-ink"><x-pictogram :name="$criterion['icon']" class="h-6 w-6" :stroke="2.2" /></span>
                        <span class="text-sm font-semibold text-ink-900">{{ $criterion['label'] }}</span>
                    </label>
                @endforeach
            </div>

            <div class="mt-5">
                <label for="notes" class="block text-sm font-semibold text-ink-900">Catatan tambahan</label>
                <textarea id="notes" name="notes" rows="3"
                          class="mt-2 w-full rounded-2xl border-0 bg-paper-dim px-4 py-3 text-sm text-ink-900 ring-1 ring-inset ring-ink-900/5 focus:ring-2 focus:ring-route-cyan"
                          placeholder="Misal: lift tersedia namun tidak ada tombol Braille.">{{ old('notes', $features->notes) }}</textarea>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex h-13 items-center gap-2 rounded-2xl bg-go-700 px-7 py-3.5 text-sm font-extrabold text-white transition hover:bg-go-600">
                {{ $place->exists ? 'Simpan Perubahan' : 'Simpan Tempat' }}
            </button>
            <a href="{{ route('admin.places.index') }}" class="inline-flex h-13 items-center rounded-2xl px-5 text-sm font-bold text-ink-500 hover:text-ink-900">Batal</a>
        </div>
    </form>

    {{-- Peta pemilih lokasi --}}
    <div class="lg:sticky lg:top-28 lg:self-start">
        <div class="overflow-hidden rounded-2xl border border-ink-900/5 bg-white shadow-sm">
            <div class="border-b border-ink-900/5 px-5 py-3">
                <p class="text-sm font-bold text-ink-900">Pilih Lokasi</p>
                <p class="text-xs text-ink-900/50">Klik peta untuk mengisi latitude &amp; longitude.</p>
            </div>
            <div id="place-form-map" class="h-[420px] w-full"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('placeForm', () => ({
            lat: @js((string) old('latitude', $place->latitude)),
            lng: @js((string) old('longitude', $place->longitude)),
            weights: @js(collect(\App\Models\AccessibilityFeature::criteria())->pluck('weight', 'key')),
            features: {
                has_ramp: {{ old('has_ramp', $features->has_ramp ?? false) ? 'true' : 'false' }},
                has_lift: {{ old('has_lift', $features->has_lift ?? false) ? 'true' : 'false' }},
                has_accessible_toilet: {{ old('has_accessible_toilet', $features->has_accessible_toilet ?? false) ? 'true' : 'false' }},
                has_guiding_block: {{ old('has_guiding_block', $features->has_guiding_block ?? false) ? 'true' : 'false' }},
                wide_door: {{ old('wide_door', $features->wide_door ?? false) ? 'true' : 'false' }},
                braille_lift: {{ old('braille_lift', $features->braille_lift ?? false) ? 'true' : 'false' }},
            },
            map: null,
            marker: null,

            get score() {
                let earned = 0, total = 0;
                for (const [key, weight] of Object.entries(this.weights)) {
                    total += weight;
                    if (this.features[key]) earned += weight;
                }
                return total ? Math.round(earned / total * 100) : 0;
            },

            init() {
                this.$nextTick(() => this.initMap());
            },

            initMap() {
                const el = document.getElementById('place-form-map');
                if (!el || typeof window.L === 'undefined') return;

                const start = [parseFloat(this.lat) || -6.21, parseFloat(this.lng) || 106.78];
                this.map = L.map(el, { center: start, zoom: 12 });
                window.AksesKitaMap.baseTiles().addTo(this.map);

                if (this.lat && this.lng) this.setMarker(start[0], start[1]);

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
        }));
    });
</script>
@endpush
