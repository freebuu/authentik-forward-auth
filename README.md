# Authentik Forward Auth

Laravel guard + user provider for Authentik Forward Auth.

## Install

```bash
composer require freebuu/authentik-forward-auth
```

## Simple configuration
 - Read article for [Authentik configuration](https://docs.goauthentik.io/add-secure-apps/providers/proxy/forward_auth/).
 - Add a guard + provider to `config/auth.php`:

```php
// config/auth.php

'guards' => [
    //...other guards
    'authentik' => [
        'driver' => 'authentik',
        'provider' => 'authentik_generic',
    ],
],

'providers' => [
    //...other providers...
    'authentik_generic' => [
        'driver' => 'eloquent_authentik',
    ],
],
```
All done, you can now use the guard as `auth:authentik` in middlewre. 

With this configuration you receive [AuthentikUser](src/Entity/AuthentikUser.php) from `auth('authentik')->user()`

## Eloquent 
To be continued...
