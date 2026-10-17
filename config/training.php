<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brand Configuration
    |--------------------------------------------------------------------------
    */
    'brand_name' => env('APP_NAME', 'Training Defensive Driving'),

    /*
    |--------------------------------------------------------------------------
    | Centralized WhatsApp Configuration
    |--------------------------------------------------------------------------
    |
    | When disabled or when the number is empty, no WhatsApp URLs or active
    | chat links will be rendered. All WhatsApp links must be generated
    | via App\Services\WhatsAppService to ensure safe normalization.
    |
    */
    'whatsapp' => [
        'number' => env('TRAINING_WHATSAPP_NUMBER', '08118500177'),
        'enabled' => (bool) env('TRAINING_WHATSAPP_ENABLED', true),
        'callout_contacts' => array_filter(array_map('trim', explode(',', (string) env(
            'TRAINING_WHATSAPP_CALLOUT_CONTACTS',
            '08118500177,081399810272,087894580806,087894580649'
        )))),
        'default_message' => env(
            'TRAINING_WHATSAPP_DEFAULT_MESSAGE',
            'Halo, saya ingin konsultasi mengenai program Training Defensive Driving.'
        ),
        'individual_message' => env(
            'TRAINING_WHATSAPP_INDIVIDUAL_MESSAGE',
            'Halo, saya ingin bertanya mengenai program Training Defensive Driving untuk pengemudi individu.'
        ),
        'corporate_message' => env(
            'TRAINING_WHATSAPP_CORPORATE_MESSAGE',
            'Halo, kami dari perusahaan ingin mendiskusikan kebutuhan program Training Defensive Driving untuk pengemudi dan armada kami.'
        ),
    ],

];
