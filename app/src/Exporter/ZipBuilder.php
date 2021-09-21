<?php

declare(strict_types=1);

namespace App\Exporter;

use Exception;
use Symfony\Component\Yaml\Yaml;
use ZipStream\ZipStream;

class ZipBuilder
{
    protected array $formats = ['json', 'yaml'];

    public function build(ZipStream $zip, string $format, array $languages): void
    {
        if (! \in_array($format, $this->formats, true)) {
            throw new Exception(sprintf('Format %s not available', $format));
        }

        switch ($format) {
            case 'json':
                foreach ($languages as $iso => $translations) {
                    $encoded = json_encode($translations, JSON_PRETTY_PRINT);
                    if (!$encoded) {
                        continue;
                    }
                    $zip->addFile($iso.'.json', $encoded);
                }

                break;
            case 'yaml':
                $zip->addFile('translations.yaml', Yaml::dump($languages));
        }

        $zip->finish();
    }
}
