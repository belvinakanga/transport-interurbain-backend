<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'firebase' => [
        /*
         * Compte de service Firebase, au format JSON téléchargé depuis la
         * console (Paramètres du projet → Comptes de service).
         *
         * C'est lui qui autorise l'API FCM HTTP v1. Le chemin est relatif
         * à la racine du projet ; le fichier doit rester hors de Git —
         * storage/app/private est déjà exclu.
         *
         * Absent, les notifications restent enregistrées en base et
         * visibles dans l'application : seule l'alerte poussée hors
         * application manque.
         */
        'compte_service' => env(
            'FIREBASE_CREDENTIALS',
            'storage/app/private/firebase/compte-service.json'
        ),

        /*
         * Ancienne clé serveur, pour l'API « legacy » que Google a fermée.
         * Conservée le temps que les installations existantes basculent sur
         * le compte de service ci-dessus, qui a la priorité.
         */
        'cle_serveur' => env('FIREBASE_SERVER_KEY'),
    ],

    'openpay' => [
        /*
         * Interrupteur du paiement en ligne.
         *
         * À faux, les routes OpenPay et son webhook ne sont pas déclarés :
         * l'achat est validé directement, sans paiement réel. C'est le
         * mode attendu pendant les tests fonctionnels.
         *
         * Le code OpenPay reste en place ; repasser OPENPAY_ACTIF à true
         * suffit à le rebrancher.
         */
        'actif' => filter_var(
            env('OPENPAY_ACTIF', false),
            FILTER_VALIDATE_BOOLEAN
        ),

        'api_key' => env('OPENPAY_API_KEY'),
        'base_url' => env('OPENPAY_BASE_URL', 'https://api.openpay-cg.com/v1'),
        'callback_url' => env('OPENPAY_CALLBACK_URL'),
    ],

];
