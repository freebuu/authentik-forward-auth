<?php

namespace FreeBuu\ForwardAuth\Auth;

use FreeBuu\ForwardAuth\Traits\NotUseTrait;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

final class AuthentikGuard implements Guard
{
    use GuardHelpers;
    use NotUseTrait;

    protected Request $request;

    protected bool $attempted = false;
    protected string $headerPrefix;

    public function __construct(
        Request $request,
        string $headerPrefix,
        UserProvider $provider,
    ) {
        $this->request = $request;
        $this->headerPrefix = $headerPrefix;
        $this->setProvider($provider);
    }

    public function user()
    {
        if ($this->attempted || $this->user) {
            return $this->user;
        }
        $this->attempted = true;

        return $this->user = $this
            ->getProvider()
            ->retrieveByCredentials($this->credentialsFromRequest());
    }

    public function id()
    {
        $user = $this->attempted ? $this->user : $this->user();

        return $user?->getAuthIdentifier();
    }

    public function validate(array $credentials = []): bool
    {
        $this->mustNotUse(__METHOD__);
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function credentialsFromRequest()
    {
        return collect($this->request->headers->all())
            ->filter(fn ($value, $key) => str_starts_with($key, $this->headerPrefix))
            ->mapWithKeys(fn ($value, $key) => [str_replace($this->headerPrefix, '', $key) => $value])
            ->toArray();
    }
}
