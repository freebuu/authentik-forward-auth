<?php

namespace FreeBuu\ForwardAuth\Auth;

use FreeBuu\ForwardAuth\Entity\AuthentikUser;
use Illuminate\Database\Eloquent\Model;

class UserModelRepository
{
    protected ?Model $resolvedModel = null;

    public function __construct(
        protected ?string $model
    ) {
    }

    public function findFor(AuthentikUser $authentikUser): Model
    {
        return $this->makeModel()?->newModelQuery()->where(
            $authentikUser->getAuthIdentifierName(),
            $authentikUser->getAuthIdentifier()
        )->first() ?? $this->resolvedModel;
    }

    public function sync(AuthentikUser $authentikUser, Model $user): Model
    {
        if (! $user->exists) {
            $user->{$authentikUser->getAuthIdentifierName()} = $authentikUser->getAuthIdentifier();
        }
        $user->fill($authentikUser->toArray())->save();

        return $user;
    }

    public function isModelSet(): bool
    {
        return $this->makeModel() !== null;
    }

    private function makeModel(): ?Model
    {
        try{
            return $this->resolvedModel ??= new $this->model;
        }catch (\Error){
            return null;
        }
    }
}