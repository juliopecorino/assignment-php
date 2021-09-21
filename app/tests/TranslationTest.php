<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Key;
use App\Entity\Language;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class TranslationTest extends AbstractApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollectionNotAuth(): void
    {
        // User with no token.
        static::createClient()->request('GET', '/api/translations');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetCollectionAdmin(): void
    {
        $response = $this->createClientAdmin()->request('GET', '/api/languages');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Language',
            '@id' => '/api/languages',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 5,
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Language::class);
    }

    public function testGetCollectionReader(): void
    {
        $response = $this->createClientReader()->request('GET', '/api/languages');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Language',
            '@id' => '/api/languages',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 5,
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Language::class);
    }

    public function testUpdateTranslation(): void
    {
        $keyIri = $this->findIriBy(Key::class, [
            'name' => 'main.welcome',
        ]);

        /** @var Key $key */
        $key = $this->getEntityManager()->getRepository(Key::class)->findOneBy([
            'name' => 'main.hello',
        ]);

        $response = $this->createClientAdmin()->request(
            'POST',
            $keyIri.'/es',
            [
                'query' => [
                    'text' => 'great in spanish',
                ],
            ]
        );
        $this->assertSame('Updated', $response->getContent());
        $this->assertResponseIsSuccessful();

        $response = $this->createClientAdmin()->request(
            'POST',
            $keyIri.'/fr_FR',
            [
                'query' => [
                    'text' => 'great in french',
                ],
            ]
        );
        $this->assertSame('Updated', $response->getContent());
        $this->assertResponseIsSuccessful();

        $response = $this->createClientAdmin()->request(
            'POST',
            $keyIri.'/aaaaa',
            [
                'query' => [
                    'text' => 'great in french',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(500);
    }
}
