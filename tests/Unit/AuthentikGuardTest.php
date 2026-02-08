<?php

namespace Tests\Unit;

use FreeBuu\ForwardAuth\Auth\AuthentikGuard;
use FreeBuu\ForwardAuth\Auth\AuthentikUserProvider;
use FreeBuu\ForwardAuth\Entity\AuthentikUser;
use Illuminate\Http\Request;
use Tests\TestCase;

class AuthentikGuardTest extends TestCase
{
    protected Request $request;

    protected string $headerPrefix;

    protected AuthentikGuard $guard;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new Request;
        $this->headerPrefix = 'x-test-header-';
        $mockProvider = $this->createMock(AuthentikUserProvider::class);
        $mockProvider
            ->method('retrieveByCredentials')
            ->willReturnCallback(fn ($credentials) => new AuthentikUser(
                $credentials,
                'uid'
            ));

        $this->guard = new AuthentikGuard(
            $this->request,
            $this->headerPrefix,
            $mockProvider
        );
    }

    public function test_it_can_retrieve_credentials_from_request()
    {
        $this->injectHeadersToRequest([
            'some-other-header' => 'some-value',
            'one-more-header' => 'another-value',
        ], false);

        $this->injectHeadersToRequest($credentials = [
            'username' => 'test',
            'email' => 'test@example.com',
            'uid' => '1234567890',
        ], true);
        $this->assertSame($credentials, $this->guard->credentialsFromRequest());
    }

    public function test_attempt_only_once()
    {
        $this->injectHeadersToRequest([
            'uid' => $uid = '1234567890',
        ], true);
        $this->assertSame($uid, $this->guard->user()->getAuthIdentifier());
        $this->injectHeadersToRequest([
            'uid' => '321',
        ], true);
        $this->assertSame($uid, $this->guard->user()->getAuthIdentifier());
        $this->assertSame($uid, $this->guard->id());
    }

    private function injectHeadersToRequest(array $headers, bool $withPrefix): void
    {
        foreach ($headers as $key => $value) {
            $this->request->headers->set(($withPrefix ? $this->headerPrefix : '').$key, $value);
        }
    }
}
