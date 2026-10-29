@extends('layouts.app')

@section('title', 'Sondage de couleurs · Administration · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-4xl px-5 py-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-serif text-3xl font-extrabold">Sondage de couleurs</h1>

        <div class="flex gap-3">
            <a href="{{ route('admin.orders.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Commandes</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Se déconnecter</button>
            </form>
        </div>
    </div>

    <p class="mt-4 text-sm text-sourdine">Un seul vote par visiteur et par modèle. Seuls les modèles ayant reçu au moins un vote apparaissent.</p>

    @forelse ($sondages as $sondage)
        <section class="mt-8 rounded border border-ligne p-5">
            <h2 class="font-serif text-xl font-extrabold">{{ $sondage['product']->name }}</h2>
            <p class="text-sm text-sourdine">{{ $sondage['total'] }} {{ $sondage['total'] > 1 ? 'votes' : 'vote' }}</p>

            <ul class="mt-4 space-y-3">
                @foreach ($sondage['lignes'] as $ligne)
                    <li>
                        <div class="flex justify-between text-sm">
                            <span>{{ $ligne['couleur'] }}</span>
                            <span class="text-sourdine">{{ $ligne['votes'] }} · {{ $ligne['part'] }} %</span>
                        </div>
                        <div class="mt-1 h-2 rounded bg-noir-doux" role="presentation">
                            <div class="h-2 rounded bg-or" style="width: {{ $ligne['part'] }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @empty
        <p class="mt-8 rounded border border-ligne px-4 py-6 text-center text-sourdine">Aucun vote pour le moment.</p>
    @endforelse
</div>
@endsection
