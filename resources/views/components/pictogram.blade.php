@props([
    'name' => 'info',
    'class' => 'h-6 w-6',
    'stroke' => 2,
])

{{--
    Authored accessibility pictogram set — one consistent stroke weight and
    geometry, drawn to read at signage distance. Decorative by default
    (aria-hidden); wrap with a visible label or use title for meaning.
--}}
@php
    $paths = [
        // Wheelchair / accessible route
        'wheelchair' => '<circle cx="9.5" cy="4" r="2"/><path d="M9.5 7.2v5.4h4.1l2.6 5.2"/><circle cx="10.2" cy="15.4" r="4.2"/><path d="M13.6 9.2h3.4"/>',
        // Blind / low-vision — open eye with guiding line
        'vision' => '<path d="M2.6 12S6 6.6 12 6.6 21.4 12 21.4 12 18 17.4 12 17.4 2.6 12 2.6 12Z"/><circle cx="12" cy="12" r="2.6"/><circle cx="12" cy="12" r="0.4"/>',
        // Guiding block (tactile paving)
        'guiding-block' => '<rect x="4" y="4" width="16" height="16" rx="3"/><circle cx="9" cy="9" r="1.1"/><circle cx="15" cy="9" r="1.1"/><circle cx="9" cy="15" r="1.1"/><circle cx="15" cy="15" r="1.1"/>',
        // Ramp
        'ramp' => '<path d="M3 19h18"/><path d="M4 19 20 6"/><path d="M4 19V9"/>',
        // Lift / elevator
        'lift' => '<rect x="4" y="3" width="16" height="18" rx="2.4"/><path d="M12 3v18"/><path d="M8 9l-1.6 2.4h3.2L8 9Z"/><path d="M16 15l1.6-2.4h-3.2L16 15Z"/>',
        // Accessible toilet
        'toilet' => '<circle cx="7" cy="5" r="1.8"/><path d="M7 8v6h3l1.8 6"/><circle cx="16.4" cy="5" r="1.8"/><path d="M16.4 8v11"/><path d="M14 11h4.8"/>',
        // Wide door
        'door' => '<path d="M6 20h12"/><rect x="6.6" y="3.4" width="10.8" height="16.6" rx="1.4"/><circle cx="14" cy="12" r="1"/>',
        // Braille
        'braille' => '<circle cx="9" cy="6" r="1.4"/><circle cx="15" cy="6" r="1.4"/><circle cx="9" cy="12" r="1.4"/><circle cx="15" cy="12" r="1.4"/><circle cx="9" cy="18" r="1.4"/><circle cx="15" cy="18" r="1.4"/>',
        // Deaf / hard of hearing
        'hearing' => '<path d="M8 8.5a3.4 3.4 0 0 1 6.5 1.2c0 2.3-2.6 2.6-2.6 5"/><path d="M12.4 19.2a4.6 4.6 0 0 1-3.3 1"/><path d="M16.4 4.6a7.4 7.4 0 0 1 3.4 6.1"/><path d="M15.4 8a4 4 0 0 1 1.6 3.2"/>',
        // Elderly / person with cane
        'elderly' => '<circle cx="11" cy="4.4" r="2"/><path d="M10.4 7.6 9 13.4l3 1.4.8 5.2"/><path d="M12 14.8l2.6 4.6"/><path d="M6.6 8.6l3.4-1.2"/><path d="M16 21 14.4 9"/>',
        // Star / friendly
        'star' => '<path d="m12 3.6 2.5 5.2 5.7.8-4.1 4 1 5.7-5.1-2.7-5.1 2.7 1-5.7-4.1-4 5.7-.8L12 3.6Z"/>',
        // Warning triangle
        'warning' => '<path d="M12 4 21 19H3L12 4Z"/><path d="M12 10v4"/><circle cx="12" cy="16.6" r="0.5"/>',
        // Blocked / unsafe
        'blocked' => '<circle cx="12" cy="12" r="8.4"/><path d="M6.4 6.4l11.2 11.2"/>',
        // Map pin
        'pin' => '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/>',
        // Report / camera
        'report' => '<path d="M4 8.4A2 2 0 0 1 6 6.4h1.6l1.2-1.8h6.4l1.2 1.8H18a2 2 0 0 1 2 2v8.4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/><circle cx="12" cy="12.6" r="3.2"/>',
        // Check / verified
        'check' => '<path d="m4.5 12.5 5 5 10-11"/>',
        // Arrow (direction)
        'arrow' => '<path d="M4 12h15"/><path d="m13 6 6 6-6 6"/>',
        // Search
        'search' => '<circle cx="11" cy="11" r="6.4"/><path d="m20 20-4.3-4.3"/>',
        // People / community
        'community' => '<circle cx="8.4" cy="8" r="2.6"/><path d="M3.4 19.4c0-2.8 2.2-5 5-5s5 2.2 5 5"/><circle cx="17" cy="9" r="2.1"/><path d="M15.6 14.7c2.4.3 4.3 2.3 4.3 4.7"/>',
        // Info
        'info' => '<circle cx="12" cy="12" r="8.4"/><path d="M12 11v5.4"/><circle cx="12" cy="8" r="0.6"/>',
        // Zoom / locate
        'locate' => '<circle cx="12" cy="12" r="7"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/><circle cx="12" cy="12" r="2.2"/>',
        // Ruler / measured
        'ruler' => '<rect x="3" y="8.6" width="18" height="6.8" rx="1.4"/><path d="M7 8.6v3M11 8.6v4M15 8.6v3M19 8.6v4"/>',
        // Layers / checklist
        'layers' => '<path d="m12 3 8 4.4-8 4.4-8-4.4L12 3Z"/><path d="m4 12 8 4.4 8-4.4"/><path d="m4 16.6 8 4.4 8-4.4"/>',
    ];

    $inner = $paths[$name] ?? $paths['info'];
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round"
     stroke-linejoin="round" aria-hidden="true" focusable="false">
    {!! $inner !!}
</svg>
