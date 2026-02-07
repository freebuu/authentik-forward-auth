<?php

namespace FreeBuu\ForwardAuth\Auth;

use FreeBuu\ForwardAuth\Entity\AuthentikUser;
use FreeBuu\ForwardAuth\Entity\PropertyMapper;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class AuthentikUserProvider implements UserProvider
{
    public function __construct(
        protected string $authIdentifierName,
        protected array|\Closure $mapper,
        protected array $validationRules,
        protected bool $create = true,
        protected ?string $model = null,
    ) {}

    public function retrieveByCredentials(array $credentials)
    {
        $attributes = is_array($this->mapper)
            ? PropertyMapper::byArray($this->mapper, $credentials)
            : call_user_func($this->mapper, $credentials);
        if (! empty($this->validationRules)) {
            $validator = Validator::make($attributes, $this->validationRules);
            if ($validator->fails()) {
                return null;
            }
        }
        $authentikUser = new AuthentikUser($attributes, $this->authIdentifierName);
        if (! $model = $this->makeModel()) {
            return $authentikUser;
        }
        if (empty($authentikUser->getAuthIdentifier())) {
            return null;
        }
        $user = $model->newModelQuery()->where(
            $authentikUser->getAuthIdentifierName(),
            $authentikUser->getAuthIdentifier()
        )->first();
        if (is_null($user) && $this->create === false) {
            return null;
        }
        /** @var (Model&Authenticatable) $user */
        $user = $user ?? $model;
        if (! $user->exists) {
            $user->{$authentikUser->getAuthIdentifierName()} = $authentikUser->getAuthIdentifier();
        }
        $user->fill($attributes)->save();

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

    private function makeModel(): ?Model
    {
        if (! $this->model) {
            return null;
        }

        return new $this->model;
    }
}
