<?php

namespace FreeBuu\ForwardAuth\Auth;

use FreeBuu\ForwardAuth\Entity\AuthentikUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class AuthentikUserProvider implements UserProvider
{
    public function __construct(
        protected string $authIdentifierName,
        protected array|string $mapper,
        protected array $validationRules,
        protected bool $create,
        protected UserModelRepository $modelRepository,
    ) {}

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($attributes = $this->validateAttributes($credentials))) {
            return null;
        }
        $authentikUser = new AuthentikUser($attributes, $this->authIdentifierName);
        if (! $this->modelRepository->isModelSet()) {
            return $authentikUser;
        }
        $user = $this->modelRepository->findFor($authentikUser);
        if (! $user->exists && ! $this->create) {
            return null;
        }
        $this->modelRepository->sync($authentikUser, $user);

        return $user;
    }

    public function retrieveById($identifier)
    {
        return null;
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token) {}

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return false;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): bool
    {
        return false;
    }

    private function validateAttributes(array $credentials): array
    {
        $attributes = $this->processWithCallback($credentials);
        if (empty($attributes)) {
            return [];
        }
        $validator = Validator::make(
            $attributes,
            array_merge([$this->authIdentifierName => 'required'], $this->validationRules)
        );
        if ($validator->fails()) {
            return [];
        }

        return $attributes;
    }

    private function processWithCallback(array $credentials): array
    {
        if (is_string($this->mapper)) {
            $callback = App::make($this->mapper);
        } else {
            $callback = function ($credentials) {
                return array_map(function ($headerField) use ($credentials) {
                    return $credentials[$headerField] ?? null;
                }, $this->mapper);
            };
        }
        if (! is_callable($callback)) {
            return [];
        }

        return $callback($credentials);
    }
}
