@extends('layouts.app')

@section('title', 'Stock '.$product->name.' · Administration · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-4xl px-5 py-10">
    <a href="{{ route('admin.stock.index') }}" class="text-sm text-sourdine hover:text-ivoire">← Retour au stock</a>
    <h1 class="mt-3 font-serif text-3xl font-extrabold">Stock · {{ $product->name }}</h1>

    @if ($errors->any())
        <p class="mt-6 rounded border border-red-400 px-4 py-2 text-sm text-red-400" role="alert">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('admin.stock.update', $product) }}" class="mt-6">
        @csrf
        @method('PUT')

        <div class="overflow-x-auto rounded border border-ligne">
            <table class="w-full text-left text-sm">
                <thead class="bg-noir-doux text-sourdine">
                    <tr>
                        <th class="px-4 py-3 font-medium">Couleur</th>
                        @foreach ($product->sizes as $size)
                            <th class="px-4 py-3 font-medium">{{ $size->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-ligne">
                    @foreach ($product->colors as $color)
                        <tr>
                            <th scope="row" class="px-4 py-3 font-medium">{{ $color->name }}</th>
                            @foreach ($product->sizes as $size)
                                <td class="px-4 py-3">
                                    <input type="number" min="0" step="1"
                                           name="quantites[{{ $color->id }}][{{ $size->id }}]"
                                           value="{{ $quantites[$color->id.'-'.$size->id] ?? '' }}"
                                           aria-label="{{ $color->name }}, taille {{ $size->name }}"
                                           class="w-20 rounded border border-ligne bg-noir-doux px-2 py-1 text-ivoire">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="submit" class="mt-6 rounded bg-or px-5 py-2.5 font-medium text-noir hover:opacity-90">Enregistrer le stock</button>
    </form>
</div>
@endsection
