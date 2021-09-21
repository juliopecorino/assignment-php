<?php

declare(strict_types=1);

namespace App\Tests\Exporter;

use App\Exporter\ZipBuilder;
use App\Tests\AbstractApiTestCase;
use Exception;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ZipStream\Option\Archive as ArchiveOptions;
use ZipStream\ZipStream;

class ZipBuilderTest extends AbstractApiTestCase
{
    //use RefreshDatabaseTrait;
    public function testBuildInvalidFormat(): void
    {
        $fileName = 'test.zip';
        $builder = $this->getContainer()->get(ZipBuilder::class);
        $languages = [
            'es' => [
                'hello' => 'hola',
            ],
        ];

        $zip = new ZipStream($fileName);
        $this->expectException(Exception::class);
        $builder->build($zip, 'abc', $languages);
    }

    public function testBuildYaml(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $builder = $this->getContainer()->get(ZipBuilder::class);
        $languages = [
            'es' => [
                'hello' => 'hola',
            ],
        ];

        $zip = new ZipStream(null, $options);
        $builder->build($zip, 'yaml', $languages);
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);
        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame(1, \count($files));

        $this->assertSame(['translations.yaml'], $files);
        $content = 'es:
    hello: hola
';
        $this->assertStringEqualsFile($tmpDir.'/translations.yaml', $content);
    }

    public function testBuildJson(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $builder = $this->getContainer()->get(ZipBuilder::class);
        $languages = [
            'es' => [
                'hello' => 'hola',
            ],
            'fr' => [
                'hello' => 'salut',
            ],
        ];

        $zip = new ZipStream(null, $options);
        $builder->build($zip, 'json', $languages);
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);
        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame(2, \count($files));
        $this->assertSame(['es.json', 'fr.json'], $files);

        $content = '{
    "hello": "hola"
}';
        $this->assertStringEqualsFile($tmpDir.'/es.json', $content);

        $content = '{
    "hello": "salut"
}';
        $this->assertStringEqualsFile($tmpDir.'/fr.json', $content);
    }
}
