<?php

return [
    // Numéro WhatsApp de la marque, au format international sans « + » (ex. 22890000000).
    'whatsapp_number' => env('SHOP_WHATSAPP_NUMBER', '22800000000'),

    // Date d'ouverture du drop au format ISO avec Z (heure de Lomé = UTC), ex. 2026-12-01T18:00:00Z.
    // Utilisée par le compte à rebours de l'accueil. Valeur provisoire si .env est vide.
    'drop_date' => env('SHOP_DROP_DATE') ?: '2026-12-01T18:00:00Z',

    // Nombre de jours sans commande après lesquels un stock est dit « dormant ».
    'dormant_days' => (int) env('SHOP_DORMANT_DAYS', 30),

    // Nombre d'achats (commandes payées ou livrées) pour devenir « client fidèle ».
    'loyal_after' => (int) env('SHOP_LOYAL_AFTER', 3),

    // Compte administrateur créé par : php artisan db:seed --class=AdminSeeder
    'admin_email' => env('ADMIN_EMAIL'),
    'admin_password' => env('ADMIN_PASSWORD'),
];
