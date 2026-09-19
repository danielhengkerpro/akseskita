import './bootstrap';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import L from 'leaflet';

// Expose Leaflet globally so Blade and the map helper can use it.
window.L = L;

Alpine.plugin(intersect);
window.Alpine = Alpine;

/*
|--------------------------------------------------------------------------
| Alpine Store: Accessibility Mode
|--------------------------------------------------------------------------
| Stores the preview perspective a visitor is simulating (general /
| wheelchair / blind / elderly). Consumed cross-component. The label is the
| user-facing text; the pictogram per mode is rendered in the Blade view.
*/
Alpine.store('accessMode', {
    mode: 'umum',
    modes: {
        umum: { label: 'Mode Umum', desc: 'Tampilan standar untuk semua pengguna.' },
        kursi_roda: { label: 'Kursi Roda', desc: 'Prioritaskan ramp, lift, dan pintu lebar.' },
        tunanetra: { label: 'Tunanetra', desc: 'Prioritaskan ubin pemandu dan tombol Braille.' },
        lansia: { label: 'Lansia', desc: 'Prioritaskan jalur landai dan lift ramah.' },
    },
    set(mode) {
        this.mode = mode;
    },
});

/*
|--------------------------------------------------------------------------
| Alpine Store: a11y (Accessibility — Text Resizer)
|--------------------------------------------------------------------------
| Manages text resizer preference (100% | 125% | 150%) with automatic persistence.
*/
Alpine.store('a11y', {
    textScale: 100, // 100 | 125 | 150

    init() {
        try {
            // Bersihkan sisa storage kontras dan nyaman
            localStorage.removeItem('highContrast');
            localStorage.removeItem('ak-contrast');
            localStorage.removeItem('comfort');
            localStorage.removeItem('ak-comfort');

            // Hapus sisa class/atribut jika ada di DOM
            document.documentElement.classList.remove('high-contrast');
            document.documentElement.removeAttribute('data-contrast');
            document.documentElement.removeAttribute('data-comfort');
            if (document.body) {
                document.body.classList.remove('high-contrast');
            }

            const savedScale = parseInt(
                localStorage.getItem('ak-text-scale') || localStorage.getItem('textScale') || '100',
                10
            );
            this.textScale = [100, 125, 150].includes(savedScale) ? savedScale : 100;

            this.apply();
        } catch (e) {
            console.warn('Error reading text size settings', e);
        }
    },

    cycleTextScale() {
        if (this.textScale === 100) {
            this.textScale = 125;
        } else if (this.textScale === 125) {
            this.textScale = 150;
        } else {
            this.textScale = 100;
        }
        this.apply();
    },

    getTextLabel() {
        return `Zoom ${this.textScale}%`;
    },

    apply() {
        document.documentElement.style.fontSize = this.textScale + '%';

        try {
            localStorage.setItem('ak-text-scale', this.textScale.toString());
            localStorage.setItem('textScale', this.textScale.toString());
        } catch (e) {}
    },
});

Alpine.start();
