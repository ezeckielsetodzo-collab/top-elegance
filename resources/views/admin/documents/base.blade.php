<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') · TOP ÉLÉGANCE</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #111; margin: 0; background: #f2f2f2; }
        .barre { display: flex; gap: 12px; justify-content: center; padding: 14px; background: #111; }
        .barre a, .barre button { font: inherit; font-size: 14px; color: #fff; background: #333; border: 0; border-radius: 4px; padding: 8px 16px; cursor: pointer; text-decoration: none; }
        .barre button { background: #d4a84b; color: #111; font-weight: bold; }
        .page { max-width: 780px; margin: 24px auto; background: #fff; padding: 40px; }
        h1 { font-size: 24px; margin: 0; letter-spacing: 1px; }
        h2 { font-size: 18px; margin: 28px 0 8px; }
        .entete { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111; padding-bottom: 16px; }
        .entete p { margin: 4px 0; font-size: 14px; }
        .titre { text-align: right; }
        .titre strong { display: block; font-size: 20px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 14px; }
        th, td { text-align: left; border-bottom: 1px solid #ccc; padding: 8px 6px; vertical-align: top; }
        th { background: #f2f2f2; }
        .droite { text-align: right; }
        .total td { font-weight: bold; border-top: 2px solid #111; border-bottom: 0; }
        .bloc { margin-top: 16px; font-size: 14px; line-height: 1.6; }
        .signatures { display: flex; gap: 40px; margin-top: 56px; font-size: 14px; }
        .signatures div { flex: 1; border-top: 1px solid #111; padding-top: 6px; }
        .pied { margin-top: 40px; font-size: 12px; color: #555; text-align: center; }
        @media print {
            body { background: #fff; }
            .barre { display: none; }
            .page { margin: 0; max-width: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="barre">
        <a href="{{ url()->previous() }}">← Retour</a>
        <button type="button" onclick="window.print()">Imprimer / Enregistrer en PDF</button>
    </div>
    <main class="page">
        <div class="entete">
            <div>
                <h1>TOP ÉLÉGANCE</h1>
                <p>Lomé, Togo</p>
                <p>WhatsApp : +{{ config('shop.whatsapp_number') }}</p>
            </div>
            <div class="titre">
                <strong>@yield('document')</strong>
                @yield('reference')
            </div>
        </div>

        @yield('contenu')

        <p class="pied">TOP ÉLÉGANCE · Document généré le {{ now()->format('d/m/Y') }}</p>
    </main>
</body>
</html>
