@props(['color' => 'yellow', 'label' => null, 'size' => 'md'])

@php
    // Every tier carries a colour AND a distinct symbol shape AND a word —
    // status is never communicated by colour alone (WCAG 1.4.1).
    $tiers = [
        'green' => [
            'bg' => 'bg-go-50 text-go-700 ring-go-600/30',
            'glyph' => '<svg viewBox="0 0 12 12" class="h-3 w-3" aria-hidden="true"><path d="M6 0.6 11.4 6 6 11.4 0.6 6Z" fill="currentColor"/></svg>',
            'word' => 'Ramah',
        ],
        'yellow' => [
            'bg' => 'bg-warn-50 text-warn-700 ring-warn-600/30',
            'glyph' => '<svg viewBox="0 0 12 12" class="h-3 w-3" aria-hidden="true"><path d="M6 1 11.4 10.4H0.6L6 1Z" fill="currentColor"/></svg>',
            'word' => 'Terbatas',
        ],
        'red' => [
            'bg' => 'bg-stop-50 text-stop-700 ring-stop-600/30',
            'glyph' => '<svg viewBox="0 0 12 12" class="h-3 w-3" aria-hidden="true"><circle cx="6" cy="6" r="5.2" fill="currentColor"/></svg>',
            'word' => 'Berbahaya',
        ],
    ];

    $tier = $tiers[$color] ?? $tiers['yellow'];
    $sizes = [
        'sm' => 'px-2 py-1 text-[11px] gap-1',
        'md' => 'px-2.5 py-1.5 text-xs gap-1.5',
        'lg' => 'px-3.5 py-2 text-sm gap-2',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-lg font-extrabold uppercase tracking-wide ring-1 ring-inset {$tier['bg']} {$sizeClass}"]) }}>
    <span class="shrink-0 leading-none">{!! $tier['glyph'] !!}</span>
    <span>{{ $label ?? $tier['word'] }}</span>
</span>
