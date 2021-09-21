<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UserTest extends AbstractApiTestCase
{
    //use RefreshDatabaseTrait;
    public function testGetCollectionNoAuth(): void
    {
        static::createClient()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(401);

        $this->createClientReader()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetCollectionAdmin(): void
    {
        $response = $this->createClientAdmin()->request('GET', '/api/users');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 2,
        ]);

        $this->assertCount(2, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testCRUDNoAuth(): void
    {
        // Create
        $this->createClient()->request(
            'POST',
            '/api/users',
            [
                'json' => [
                    'email' => 'test@test.com',
                    'plainPassword' => 'test',
                    'roles' => ['ROLE_READER'],
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreate(): void
    {
        $this->createClientAdmin()->request(
            'POST',
            '/api/users',
            [
                'json' => [
                    'email' => 'test@test.com',
                    'plainPassword' => 'test',
                    'roles' => ['ROLE_READER'],
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(201);

        $iri = $this->findIriBy(User::class, [
            'email' => 'test@test.com',
        ]);

        // Read
        $this->createClientAdmin()->request(
            'GET',
            $iri
        );
        $this->assertResponseStatusCodeSame(200);
    }
}
