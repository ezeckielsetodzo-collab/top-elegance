@props(['type' => 'preorder'])
@php
    $styles = [
        'preorder' => 'bg-or text-noir',
        'soon' => 'border border-ligne text-sourdine',
        'limited' => 'border border-or text-or',
    ];
    $labels = [
        'preorder' => 'Précommande',
        'soon' => 'Bientôt',
        'limited' => 'Série limitée',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-block rounded-full px-3 py-1 text-xs font-medium ' . ($styles[$type] ?? $styles['preorder'])]) }}>
    {{ $slot->isEmpty() ? ($labels[$type] ?? '') : $slot }}
</span>