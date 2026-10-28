@props(['href' => null, 'variant' => 'primary', 'type' => 'button'])
@php
    $classes = 'inline-flex items-center justify-center rounded-sm px-6 py-3 font-medium transition disabled:cursor-not-allowed disabled:opacity-50 '
        . ($variant === 'primary'
            ? 'bg-or text-noir hover:brightness-110'
            : 'border border-ligne text-ivoire hover:border-or hover:text-or');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif