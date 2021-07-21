<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
        'authenticator' => \ByTIC\Auth\Security\Guard\Authenticator\BaseAuthenticator::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    */
    'providers' => [
        'users' => [
            'driver' => null,
            'model' => null,
        ],
        'admins' => [
            'driver' => null,
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

    /*
    |--------------------------------------------------------------------------
    | Password encoders
    |--------------------------------------------------------------------------
    |
    */
    'encoders' => [
        \Symfony\Component\Security\Core\User\UserInterface::class => [
            'algorithm' => 'auto',
            'hash_algorithm' => 'sha256',
            'cost' => 12,
        ]
    ],
];