<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Doctrine\ORM\EntityManagerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

abstract class AbstractApiTestCase extends ApiTestCase
{
    private $token;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }

    protected function createClientAdmin(): Client
    {
        $token = $this->getUserToken();

        return $this->createClientWithCredentials($token);
    }

    protected function createClientReader(): Client
    {
        $token = $this->getUserToken([
            'email' => 'reader@example.com',
            'password' => 'reader',
        ], true);

        return $this->createClientWithCredentials($token);
    }

    /**
     * Use credentials with token.
     */
    protected function getUserToken($body = [], $cleanToken = false): string
    {
        if ($cleanToken) {
            $this->token = null;
        }

        if (null !== $this->token) {
            return $this->token;
        }

        $defaultBody = [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ];

        if (! empty($body)) {
            $defaultBody = $body;
        }

        $response = static::createClient()->request(
            'POST',
            '/api/authentication_token',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($defaultBody),
            ],
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());

        $this->token = $data->token;

        return $data->token;
    }

    protected function getTmpFileStream(): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'zipstreamtest');
        $stream = fopen($tmp, 'wb+');

        return [$tmp, $stream];
    }

    protected function getTmpDir(): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'zipstreamtest');
        unlink($tmp);
        mkdir($tmp) || $this->fail('Failed to make directory');

        return $tmp;
    }

    protected function validateAndExtractZip($tmp): string
    {
        $tmpDir = $this->getTmpDir();

        $zipArch = new ZipArchive();
        $res = $zipArch->open($tmp);

        if (true !== $res) {
            $this->fail("Failed to open {$tmp}. Code: {$res}");

            return $tmpDir;
        }

        $this->assertSame(0, $zipArch->status);
        $this->assertSame(0, $zipArch->statusSys);

        $zipArch->extractTo($tmpDir);
        $zipArch->close();

        return $tmpDir;
    }

    /**
     * @return string[]
     */
    protected function getRecursiveFileList(string $path): array
    {
        $data = [];
        $path = (string) realpath($path);
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $pathLen = \strlen($path);
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            if (! is_dir($filePath)) {
                $data[] = substr($filePath, $pathLen + 1);
            }
        }

        sort($data);

        return $data;
    }

    private function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getUserToken();

        return static::createClient([], [
            'headers' => [
                'authorization' => 'Bearer '.$token,
            ],
        ]);
    }
}
