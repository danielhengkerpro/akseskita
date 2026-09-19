/**
 * AksesKita — Custom Leaflet helpers
 * ------------------------------------------------------------------
 * Berisi fungsi-fungsi pembantu untuk membuat peta interaktif
 * (marker berwarna sesuai status kelayakan, legenda, dsb).
 * Dipakai oleh halaman landing (home) dan halaman peta fullscreen.
 */
(function () {
    'use strict';

    // Colour + a distinct SHAPE/ICON + explicit text: status NEVER depends on colour alone (WCAG 2.1 SC 1.4.1).
    const STATUS = {
        green: {
            color: '#047857',
            emoji: '🟢',
            label: 'Ramah',
            symbol: '✔',
            fullLabel: '[🟢 Ramah]',
            svgIcon: '<svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8.5l3.5 3.5 6.5-8"/></svg>'
        },
        yellow: {
            color: '#b45309',
            emoji: '🟡',
            label: 'Terbatas',
            symbol: '⚠',
            fullLabel: '[🟡 Terbatas]',
            svgIcon: '<svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8 1a1 1 0 0 1 .866.5l6.5 11.5A1 1 0 0 1 14.5 14h-13a1 1 0 0 1-.866-1.5l6.5-11.5A1 1 0 0 1 8 1zm0 4.5a.75.75 0 0 0-.75.75v3a.75.75 0 0 0 1.5 0v-3A.75.75 0 0 0 8 5.5zm0 6a.875.875 0 1 0 0-1.75.875.875 0 0 0 0 1.75z" clip-rule="evenodd"/></svg>'
        },
        red: {
            color: '#b91c1c',
            emoji: '🔴',
            label: 'Berbahaya',
            symbol: '✖',
            fullLabel: '[🔴 Berbahaya]',
            svgIcon: '<svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4l8 8m0-8L4 12"/></svg>'
        },
    };

    function shapeSvg(status) {
        const meta = STATUS[status] || STATUS.yellow;
        return '<span style="display:inline-flex;align-items:center;justify-content:center;color:' + meta.color + '" title="' + meta.fullLabel + '">' + meta.svgIcon + '</span>';
    }

    function ensureLeaflet() {
        if (typeof window.L === 'undefined') {
            throw new Error('Leaflet belum dimuat. Pastikan window.L tersedia.');
        }
        return window.L;
    }

    /**
     * Buat basemap tile OpenStreetMap.
     */
    function baseTiles() {
        const L = ensureLeaflet();
        return L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &middot; AksesKita',
        });
    }

    /**
     * Buat marker pin kustom berwarna sesuai status, dilengkapi icon khusus untuk ramah buta warna.
     */
    function statusIcon(status) {
        const L = ensureLeaflet();
        const meta = STATUS[status] || STATUS.yellow;

        return L.divIcon({
            className: 'ak-marker',
            html:
                '<div class="ak-marker__pin" style="background:' + meta.color + '" role="img" aria-label="Status: ' + meta.label + ' (' + meta.symbol + ')">' +
                '<span aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:100%;height:100%">' + meta.svgIcon + '</span>' +
                '</div>',
            iconSize: [34, 34],
            iconAnchor: [17, 34],
            popupAnchor: [0, -30],
        });
    }

    /**
     * Konten popup HTML untuk sebuah tempat.
     */
    function popupHtml(place) {
        const safe = function (str) {
            return String(str == null ? '' : str).replace(/[&<>"]/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
            });
        };
        const meta = STATUS[place.status_color] || STATUS.yellow;

        return (
            '<article style="min-width:220px;font-family:sans-serif">' +
            '<div style="font-weight:800;font-size:1rem;line-height:1.25;margin-bottom:3px;color:#0f172a">' +
            safe(place.name) +
            '</div>' +
            '<div style="color:#64748b;font-size:0.75rem;margin-bottom:6px;font-weight:600">' +
            safe(place.category_label) +
            '</div>' +
            '<div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;font-size:0.85rem">' +
            '<span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:9999px;background:' + meta.color + ';color:#fff;font-size:0.75rem;font-weight:800">' +
            meta.symbol + ' ' + meta.label +
            '</span>' +
            '<strong style="color:#0f172a">' + place.overall_score + '/100</strong>' +
            '</div>' +
            '<p style="font-size:0.75rem;color:#475569;margin-bottom:8px;line-height:1.4">' +
            safe(place.address) +
            '</p>' +
            '<a href="' +
            place.url +
            '" style="display:inline-block;background:#0f172a;color:#fff;padding:6px 12px;border-radius:8px;text-decoration:none;font-weight:700;font-size:0.75rem">' +
            'Lihat Detail Fasilitas &rarr;' +
            '</a>' +
            '</article>'
        );
    }

    /**
     * Tambahkan daftar tempat ke peta sebagai marker.
     * Mengembalikan object markerLayer (FeatureGroup).
     */
    function addPlaces(map, places) {
        const L = ensureLeaflet();
        const group = L.featureGroup().addTo(map);

        places.forEach(function (place) {
            const meta = STATUS[place.status_color] || STATUS.yellow;
            const marker = L.marker([place.latitude, place.longitude], {
                icon: statusIcon(place.status_color),
                title: place.name + ' — ' + meta.fullLabel,
                alt: place.name + ' — Status: ' + meta.label,
            });
            marker.bindPopup(popupHtml(place));
            group.addLayer(marker);
        });

        return group;
    }

    /**
     * Buat kontrol legenda ramah buta warna (Hijau Centang, Kuning Tanda Seru, Merah Silang).
     */
    function legendControl() {
        const L = ensureLeaflet();
        return L.control({ position: 'bottomright' });
    }

    function renderLegend() {
        const control = legendControl();
        control.onAdd = function () {
            const div = L.DomUtil.create('div', 'ak-legend');
            div.setAttribute('role', 'region');
            div.setAttribute('aria-label', 'Legenda status aksesibilitas ramah buta warna');
            div.style.cssText =
                'background:#ffffff;padding:10px 14px;border-radius:12px;border:1px solid #e2e8f0;' +
                'box-shadow:0 10px 25px -8px rgba(15,23,42,.25);font-size:11px;line-height:1.7;color:#0f172a';
            div.innerHTML =
                '<div style="font-weight:800;margin-bottom:4px;font-size:11px;text-transform:uppercase;letter-spacing:0.04em;color:#334155">Legenda Akses</div>' +
                '<div style="display:flex;align-items:center;gap:6px;font-weight:700;color:#047857"><span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:4px;background:#047857;color:#fff;font-size:10px">✔</span> [🟢 Ramah] (&ge;75)</div>' +
                '<div style="display:flex;align-items:center;gap:6px;font-weight:700;color:#b45309"><span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:4px;background:#b45309;color:#fff;font-size:10px">⚠</span> [🟡 Terbatas] (50–74)</div>' +
                '<div style="display:flex;align-items:center;gap:6px;font-weight:700;color:#b91c1c"><span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:4px;background:#b91c1c;color:#fff;font-size:10px">✖</span> [🔴 Berbahaya] (&lt;50)</div>';
            L.DomEvent.disableClickPropagation(div);
            return div;
        };
        return control;
    }

    window.AksesKitaMap = {
        STATUS: STATUS,
        baseTiles: baseTiles,
        statusIcon: statusIcon,
        popupHtml: popupHtml,
        addPlaces: addPlaces,
        renderLegend: renderLegend,
    };
})();
