<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frais de service
    |--------------------------------------------------------------------------
    |
    | Montant fixe ajouté au prix de chaque billet vendu, quel que soit le
    | trajet. C'est la part que le voyageur paie en plus du prix de
    | l'agence, et la seule que l'application lui montre.
    |
    | Ces frais se répartissent ensuite entre Tokende et l'agence. Cette
    | répartition est interne : elle ne doit jamais apparaître côté client.
    |
    | « commission » + « part_agence » doivent totaliser « par_billet ».
    |
    */

    'frais_service' => [

        /** Ce que le voyageur paie en plus, par billet. */
        'par_billet' => (float) env('TOKENDE_FRAIS_PAR_BILLET', 100),

        /** Part revenant à Tokende. */
        'commission' => (float) env('TOKENDE_COMMISSION_PAR_BILLET', 80),

        /** Part reversée à l'agence. */
        'part_agence' => (float) env('TOKENDE_PART_AGENCE_PAR_BILLET', 20),

    ],

];
