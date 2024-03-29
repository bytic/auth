<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    */
    'providers' => [
        'users' => [
            'model' => null,
        ],
        'admins' => [
            'model' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authenticators
    |--------------------------------------------------------------------------
    |
    */
    'authenticators' => [
        'jwt' => [
            'class' => \ByTIC\Auth\Security\Authenticator\JwtAuthenticator::class
        ]
    ],
];