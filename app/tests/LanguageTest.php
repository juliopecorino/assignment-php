<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Language;
use App\Repository\LanguageRepository;
use DateTime;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class LanguageTest extends AbstractApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollectionNotAuth(): void
    {
        // User with no token.
        static::createClient()->request('GET', '/api/languages');
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

    public function testCreate(): void
    {
        $repo = $this->getEntityManager()->getRepository(Language::class);
        $original = $repo->count([]);
        $language = (new Language())
            ->setName('Russian')
            ->setIsoCode('ru')
            ->setLtr(true)
            ->setCreatedAt(new DateTime())
        ;
        $this->getEntityManager()->persist($language);
        $this->getEntityManager()->flush();

        $this->assertSame($original + 1, $repo->count([]));

        $this->getEntityManager()->remove($language);
        $this->getEntityManager()->flush();

        $this->assertSame($original, $repo->count([]));
    }

    public function testGetLanguageList(): void
    {
        /** @var LanguageRepository $repo */
        $repo = $this->getContainer()->get(LanguageRepository::class);
        $list = $repo->getLanguagesList();

        $this->assertIsArray($list);
        $this->assertCount(5, $list);

        $this->assertNotNull($list['en']);
        $this->assertSame(2, \count($list['en']));
        $this->assertSame('Welcome!', $list['en']['main.welcome']);
        $this->assertSame('Hello', $list['en']['main.hello']);

        $this->assertSame('Bienvenido!', $list['es']['main.welcome']);
        $this->assertSame('Hola', $list['es']['main.hello']);
    }

    public function testExportJson(): void
    {
        $this->createClientReader()->request('GET', '/api/languages/export/json.zip');
        $this->assertResponseIsSuccessful();
    }

    public function testExportYaml(): void
    {
        $this->createClientReader()->request('GET', '/api/languages/export/yaml.zip');
        $this->assertResponseIsSuccessful();
    }

    public function testExportWrongFormat(): void
    {
        $this->createClientReader()->request('GET', '/api/languages/export/haha.zip');
        $this->assertResponseStatusCodeSame(500);

        $this->createClientReader()->request('GET', '/api/languages/export/aaa');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testExportNoLanguages(): void
    {
        // Test clean languages.
        $repo = $this->getContainer()->get(LanguageRepository::class);
        $languages = $repo->findAll();
        foreach ($languages as $language) {
            $repo->remove($language);
        }
        $languages = $repo->findAll();
        $this->assertSame(0, \count($languages));

        $this->createClientReader()->request('GET', '/api/languages/export/yaml.zip');
        $this->assertResponseStatusCodeSame(403);
    }
}
