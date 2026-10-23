<?php

return [
    // Numéro WhatsApp de la marque, au format international sans « + » (ex. 22890000000).
    'whatsapp_number' => env('SHOP_WHATSAPP_NUMBER', '22800000000'),

    // Date d'ouverture du drop (ex. 2026-11-15 18:00:00), utilisée pour le compte à rebours.
    'drop_date' => env('SHOP_DROP_DATE'),
];