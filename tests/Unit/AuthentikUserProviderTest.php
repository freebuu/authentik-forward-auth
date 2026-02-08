<?php

namespace Tests\Unit;

use FreeBuu\ForwardAuth\Auth\AuthentikUserProvider;
use FreeBuu\ForwardAuth\Entity\PropertyMapper;
use Tests\TestCase;

class AuthentikUserProviderTest extends TestCase
{
    protected string $authIdentifierName = 'authentik_id';

    protected array|string $mapper = [];

    protected array $validationRules = [];

    protected bool $create = true;

    protected ?string $model = null;

    public function test_it_map_attributes_from_array()
    {
        $this->mapper = [$this->authIdentifierName => 'uid'];
        $user = $this->makeProvider()->retrieveByCredentials([
            'uid' => '123',
            'some-key' => 'some-value',
        ]);
        $this->assertSame('123', $user->getAuthIdentifier());
        $this->assertNull($user->get('some-key'));
    }

    public function test_it_validates_credentials()
    {
        $this->mapper = [$this->authIdentifierName => 'uid', 'int-field' => 'int-credential'];
        $this->validationRules = [$this->authIdentifierName => 'required', 'int-field' => 'required|integer'];
        $user = $this->makeProvider()->retrieveByCredentials([
            'uid' => '123',
        ]);
        $this->assertNull($user);
        $user = $this->makeProvider()->retrieveByCredentials([
            'uid' => '123',
            'int-credential' => 432,
        ]);
        $this->assertSame('123', $user->getAuthIdentifier());
    }

    public function test_it_map_with_invokable()
    {
        PropertyMapper::$customMapper = function (array $data) {
            return array_merge($data, ['stub-mapper' => $data['to-stub-mapper'] ?? null]);
        };
        $this->mapper = PropertyMapper::class;
        $user = $this->makeProvider()->retrieveByCredentials([
            $this->authIdentifierName => '123',
            'to-stub-mapper' => 321,
        ]);
        $this->assertSame('123', $user->getAuthIdentifier());
        $this->assertSame(321, $user->get('stub-mapper'));
    }

    protected function makeProvider(): AuthentikUserProvider
    {
        return new AuthentikUserProvider(
            $this->authIdentifierName,
            $this->mapper,
            $this->validationRules,
            $this->create,
            $this->model
        );
    }
}
