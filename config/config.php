<?php

return [
    'guard-name' => env('AUTHENTIK_GUARD_NAME', 'authentik'),
    'header-prefix' => env('AUTHENTIK_HEADER_PREFIX', 'X-Authentik-'),
    'location' => env('AUTHENTIK_LOCATION', 'outpost.goauthentik.io'),
    'defaults' => [
        'create-users' => true,
        'identifier-name' => 'uid',
        'mapper' => \FreeBuu\ForwardAuth\Entity\PropertyMapper::class,
        'validation' => [],
    ],
];
