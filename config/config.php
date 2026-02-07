<?php

return [
    'guard-name' => 'authentik',
    'header-prefix' => 'X-Authentik-',
    'defaults' => [
        'create-users' => true,
        'identifier-name' => 'uid',
        'mapper' => \FreeBuu\ForwardAuth\Entity\PropertyMapper::class,
        'validation' => [],
    ],
];
