@props(['sizes' => ['S', 'M', 'L', 'XL'], 'name' => 'size', 'selected' => 'M'])

<div x-data="{ size: @js($selected) }" role="group" aria-label="Choix de la taille">
    <input type="hidden" name="{{ $name }}" :value="size">
    <div class="flex gap-2">
        @foreach ($sizes as $s)
            <button type="button"
                    @click="size = '{{ $s }}'"
                    :aria-pressed="size === '{{ $s }}'"
                    class="size-11 rounded border text-sm transition"
                    :class="size === '{{ $s }}' ? 'border-transparent bg-or text-noir' : 'border-ligne hover:border-or'">
                {{ $s }}
            </button>
        @endforeach
    </div>
</div>