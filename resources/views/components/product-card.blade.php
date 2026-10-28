@props(['product'])

<article class="flex flex-col rounded border border-ligne bg-noir-doux p-3">
    <div class="relative mb-3 grid aspect-square place-items-center rounded bg-noir">
        @if (!empty($product['image']))
            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy"
                 class="h-full w-full rounded object-cover">
        @else
            <svg viewBox="0 0 100 100" class="w-4/5" aria-hidden="true">
                <path fill="{{ $product['color'] ?? '#161616' }}" stroke="#8884"
                      d="M35 10 L15 20 L5 40 L20 46 L26 38 L26 90 L74 90 L74 38 L80 46 L95 40 L85 20 L65 10 Q50 22 35 10 Z"/>
            </svg>
        @endif

        <div class="absolute left-2 top-2 flex flex-col items-start gap-1">
            <x-badge :type="!empty($product['preorder_open']) ? 'preorder' : 'soon'" />
            @if (!empty($product['is_limited']))
                <x-badge type="limited" />
            @endif
        </div>
    </div>

    <h3 class="font-serif text-lg font-semibold">{{ $product['name'] }}</h3>
    <p class="mb-3 text-sm text-or">{{ $product['tagline'] ?? '' }}</p>

    <x-button :href="$product['url'] ?? '#'" variant="secondary" class="mt-auto py-2 text-sm">Voir</x-button>
</article>
