<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Key;
use App\Entity\Language;
use App\Entity\Translation;
use Doctrine\Common\Collections\ArrayCollection;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class KeyTest extends AbstractApiTestCase
{
    //use RefreshDatabaseTrait;
    public function testGetCollectionNoAuth(): void
    {
        static::createClient()->request('GET', '/api/keys');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetCollectionAdmin(): void
    {
        $response = $this->createClientAdmin()->request('GET', '/api/keys');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Key',
            '@id' => '/api/keys',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 3,
        ]);

        $this->assertCount(3, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Key::class);
    }

    public function testGetCollectionReader(): void
    {
        $response = $this->createClientReader()->request('GET', '/api/keys');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Key',
            '@id' => '/api/keys',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 3,
        ]);

        $this->assertCount(3, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Key::class);
    }

    public function testCRUDNoAuth(): void
    {
        // Create
        $this->createClient()->request(
            'POST',
            '/api/keys',
            [
                'json' => [
                    'name' => 'my_key',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(401);

        $iri = $this->findIriBy(Key::class, [
            'name' => 'main.hello',
        ]);

        // Read
        $this->createClient()->request(
            'GET',
            $iri
        );
        $this->assertResponseStatusCodeSame(401);

        // Update
        $this->createClient()->request(
            'PUT',
            $iri,
            [
                'json' => [
                    'name' => 'my_key',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(401);

        // Delete
        $this->createClient()->request(
            'DELETE',
            $iri
        );
        $this->assertResponseStatusCodeSame(401);
    }

    public function testCRUDReader(): void
    {
        // Create
        $this->createClientReader()->request(
            'POST',
            '/api/keys',
            [
                'json' => [
                    'name' => 'my_key',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(403);

        $iri = $this->findIriBy(Key::class, [
            'name' => 'main.hello',
        ]);

        // Read
        $this->createClientReader()->request(
            'GET',
            $iri
        );
        $this->assertResponseStatusCodeSame(200);

        // Update
        $this->createClientReader()->request(
            'PUT',
            $iri,
            [
                'json' => [
                    'name' => 'my_key',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(403);

        // Delete
        $this->createClientReader()->request(
            'DELETE',
            $iri
        );
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateKey(): void
    {
        $this->createClientAdmin()->request(
            'POST',
            '/api/keys',
            [
                'json' => [
                    'name' => 'my_key',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(
            [
                '@context' => '/api/contexts/Key',
                '@type' => 'Key',
                'name' => 'my_key',
            ]
        );
    }

    public function testCreateNotUniqueKey(): void
    {
        $client = $this->createClientAdmin();
        $client->request(
            'POST',
            '/api/keys',
            [
                'json' => [
                    'name' => 'testKey',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(201);

        $client->request(
            'POST',
            '/api/keys',
            [
                'json' => [
                    'name' => 'testKey',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(422);
        $client->request('GET', '/api/keys');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Key',
            '@id' => '/api/keys',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 5,
        ]);
    }

    public function testUpdate(): void
    {
        $client = $this->createClientAdmin();
        $iri = $this->findIriBy(Key::class, [
            'name' => 'main.hello',
        ]);
        $response = $client->request(
            'PUT',
            $iri,
            [
                'json' => [
                    'name' => 'main.hello.modified',
                ],
            ]
        );
        $this->assertResponseIsSuccessful();

        $modified = $response->toArray()['name'];
        $this->assertSame('main.hello.modified', $modified);

        $this->assertNull(
            $this->getEntityManager()->getRepository(Key::class)->findOneBy([
                'name' => 'main.hello',
            ])
        );
        $this->assertNotNull(
            $this->getEntityManager()->getRepository(Key::class)->findOneBy([
                'name' => 'main.hello.modified',
            ])
        );
    }

    public function testDelete(): void
    {
        $client = $this->createClientAdmin();
        $iri = $this->findIriBy(Key::class, [
            'name' => 'main.welcome',
        ]);

        $client->request(
            'DELETE',
            $iri
        );

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull(
            $this->getEntityManager()->getRepository(Key::class)->findOneBy([
                'name' => 'main.welcome',
            ])
        );
    }

    public function testGetTranslations(): void
    {
        $repo = $this->getEntityManager()->getRepository(Language::class);
        $language = $repo->findOneBy([
            'isoCode' => 'es',
        ]);

        $key = (new Key())
            ->setName('key_test')
        ;
        $this->getEntityManager()->persist($key);
        $this->getEntityManager()->flush();
        $this->assertSame(0, $key->getTranslations()->count());

        $translation = (new Translation())
            ->setKey($key)
            ->setLanguage($language)
            ->setText('great!!')
        ;

        $this->getEntityManager()->persist($translation);
        $this->getEntityManager()->flush();
        $this->assertSame($translation->getLanguage()->getIsoCode(), 'es');

        $collection = new ArrayCollection();
        $collection->add($translation);
        $key->setTranslations($collection);

        $this->assertSame(1, $key->getTranslations()->count());
    }
}
