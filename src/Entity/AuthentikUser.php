<?php

namespace FreeBuu\ForwardAuth\Entity;

use FreeBuu\ForwardAuth\Traits\NotUseTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Fluent;

final class AuthentikUser extends Fluent implements Authenticatable
{
    use NotUseTrait;

    protected string $authIdentifierName;

    public function __construct(array $attributes, string $authIdentifierName)
    {
        parent::__construct($attributes);
        $this->authIdentifierName = $authIdentifierName;
    }

    public function getAuthIdentifierName(): string
    {
        return $this->authIdentifierName;
    }

    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    public function getAuthPassword()
    {
        return $this->mustNotUse(__METHOD__);
    }

    public function getRememberToken()
    {
        return $this->mustNotUse(__METHOD__);
    }

    public function setRememberToken($value)
    {
        return $this->mustNotUse(__METHOD__);
    }

    public function getRememberTokenName()
    {
        return $this->mustNotUse(__METHOD__);
    }

    public function getAuthPasswordName()
    {
        return $this->mustNotUse(__METHOD__);
    }
}
