<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exporter\ZipBuilder;
use App\Repository\LanguageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

#[AsController]
class ExportLanguage extends AbstractController
{
    public function __invoke(string $format, LanguageRepository $languageRepository, ZipBuilder $builder): Response
    {
        $languages = $languageRepository->getLanguagesList();
        if (empty($languages)) {
            throw $this->createAccessDeniedException('No Languages available');
        }

        $fileName = $format.'.zip';

        return new StreamedResponse(function () use ($fileName, $format, $languages, $builder): void {
            $options = new Archive();
            $options->setContentType('application/x-zip');
            $options->setContentDisposition("attachment; filename={$fileName}");
            $zip = new ZipStream($fileName, $options);
            $builder->build($zip, $format, $languages);
        });
    }
}
