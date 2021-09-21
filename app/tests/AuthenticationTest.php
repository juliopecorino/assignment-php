<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class AuthenticationTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testLogin(): void
    {
        $client = static::createClient();

        $user = (new User())
            ->setEmail('reader_test@example.com')
            ->addRole('ROLE_READER')
            ->setPlainPassword('reader')
        ;
        $user
            ->setPassword(
                static::getContainer()->get(UserPasswordHasher::class)->hashPassword($user, 'reader')
            )
        ;

        $manager = static::getContainer()->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        // retrieve a token
        $response = $client->request('POST', '/api/authentication_token', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'reader_test@example.com',
                'password' => 'reader',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        //$client->request('GET', '/api/languages');
        //$this->assertResponseStatusCodeSame(401);

        // test authorized
       // $client->request('GET', '/api/languages', ['auth_bearer' => $json['token']]);
        //$this->assertResponseIsSuccessful();
    }
}
